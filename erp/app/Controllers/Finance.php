<?php
/**
 * PT Indo Ocean - ERP System
 * Finance & Accounting Controller
 * 
 * International standard: Double-entry bookkeeping, IFRS/PSAK compliant
 */

namespace App\Controllers;

require_once APPPATH . 'Models/FinanceModel.php';
require_once APPPATH . 'Models/ClientModel.php';
require_once APPPATH . 'Models/VesselModel.php';

use App\Models\FinanceChartOfAccountsModel;
use App\Models\FinanceCostCenterModel;
use App\Models\FinanceInvoiceModel;
use App\Models\FinanceInvoiceItemModel;
use App\Models\FinanceBillModel;
use App\Models\FinanceBillItemModel;
use App\Models\FinanceJournalEntryModel;
use App\Models\FinanceJournalLineModel;
use App\Models\FinancePaymentModel;
use App\Models\FinancePnLModel;
use App\Models\FinanceDashboardModel;
use App\Models\ClientModel;
use App\Models\VesselModel;

class Finance extends BaseController
{
    // ═══════════════════════════════════════════
    // DASHBOARD
    // ═══════════════════════════════════════════

    public function index()
    {
        $dashModel = new FinanceDashboardModel($this->db);
        $invoiceModel = new FinanceInvoiceModel($this->db);

        $stats = $dashModel->getStats();
        $trend = $dashModel->getMonthlyTrend(6);
        $recent = $dashModel->getRecentTransactions(10);
        $aging = $invoiceModel->getAgingSummary();

        $data = [
            'title' => 'Finance Dashboard',
            'currentPage' => 'finance',
            'stats' => $stats,
            'trend' => $trend,
            'recent' => $recent,
            'aging' => $aging[0] ?? [],
            'topClients' => $dashModel->getTopClientRevenue(5),
            'topVessels' => $dashModel->getTopVesselRevenue(5),
            'flash' => $this->getFlash()
        ];

        return $this->view('finance/dashboard', $data);
    }

    // ═══════════════════════════════════════════
    // INVOICES (Accounts Receivable)
    // ═══════════════════════════════════════════

    public function invoices()
    {
        $invoiceModel = new FinanceInvoiceModel($this->db);

        $filters = [
            'status' => $this->input('status'),
            'client_id' => $this->input('client_id'),
            'date_from' => $this->input('date_from'),
            'date_to' => $this->input('date_to'),
        ];

        $data = [
            'title' => 'Invoices — Accounts Receivable',
            'currentPage' => 'finance',
            'invoices' => $invoiceModel->getAllWithClient($filters),
            'filters' => $filters,
            'flash' => $this->getFlash()
        ];

        return $this->view('finance/invoices', $data);
    }

    public function createInvoice()
    {
        $invoiceModel = new FinanceInvoiceModel($this->db);
        $clientModel = new ClientModel($this->db);
        $vesselModel = new VesselModel($this->db);
        $coaModel = new FinanceChartOfAccountsModel($this->db);
        $ccModel = new FinanceCostCenterModel($this->db);

        $data = [
            'title' => 'Create Invoice',
            'currentPage' => 'finance',
            'invoice_no' => $invoiceModel->generateInvoiceNo(),
            'clients' => $clientModel->findAll([], 'name ASC'),
            'vessels' => $vesselModel->findAll([], 'name ASC'),
            'revenue_accounts' => $coaModel->getRevenueAccounts(),
            'cost_centers' => $ccModel->getActive(),
            'mode' => 'create',
            'invoice' => null,
            'items' => [],
            'flash' => $this->getFlash()
        ];

        return $this->view('finance/invoice_form', $data);
    }

    public function storeInvoice()
    {
        if (!$this->isPost()) return $this->redirect(BASE_URL . 'finance/invoices');

        $invoiceModel = new FinanceInvoiceModel($this->db);
        $itemModel = new FinanceInvoiceItemModel($this->db);
        $journalModel = new FinanceJournalEntryModel($this->db);

        $user = $this->getCurrentUser();

        // Insert invoice
        $invoiceData = [
            'invoice_no' => $this->input('invoice_no'),
            'client_id' => (int)$this->input('client_id'),
            'vessel_id' => $this->input('vessel_id') ? (int)$this->input('vessel_id') : null,
            'invoice_date' => $this->input('invoice_date'),
            'due_date' => $this->input('due_date'),
            'discount_percent' => floatval($this->input('discount_percent', 0)),
            'tax_percent' => floatval($this->input('tax_percent', 0)),
            'currency_code' => $this->input('currency_code', 'IDR'),
            'exchange_rate' => floatval($this->input('exchange_rate', 1)),
            'cost_center_id' => $this->input('cost_center_id') ? (int)$this->input('cost_center_id') : null,
            'revenue_account_id' => $this->input('revenue_account_id') ? (int)$this->input('revenue_account_id') : null,
            'terms' => $this->input('terms'),
            'notes' => $this->input('notes'),
            'status' => $this->input('action') === 'send' ? 'unpaid' : 'draft',
            'created_by' => $user['id'] ?? null
        ];

        if ($invoiceData['status'] === 'unpaid') {
            $invoiceData['sent_at'] = date('Y-m-d H:i:s');
        }

        $invoiceId = $invoiceModel->insert($invoiceData);

        if (!$invoiceId) {
            $this->setFlash('error', 'Failed to create invoice');
            return $this->redirect(BASE_URL . 'finance/create-invoice');
        }

        // Insert items
        $descriptions = $_POST['item_description'] ?? [];
        $quantities = $_POST['item_quantity'] ?? [];
        $prices = $_POST['item_price'] ?? [];
        
        foreach ($descriptions as $i => $desc) {
            if (empty($desc)) continue;
            $qty = floatval($quantities[$i] ?? 1);
            $price = floatval($prices[$i] ?? 0);
            $amount = round($qty * $price, 2);

            $itemModel->insert([
                'invoice_id' => $invoiceId,
                'description' => $desc,
                'quantity' => $qty,
                'unit_price' => $price,
                'amount' => $amount,
                'sort_order' => $i
            ]);
        }

        // Recalculate totals
        $invoiceModel->recalculateTotals($invoiceId);

        // If status is unpaid, create auto-journal
        if ($invoiceData['status'] === 'unpaid') {
            $invoice = $invoiceModel->find($invoiceId);
            $items = $itemModel->getByInvoice($invoiceId);
            $journalModel->createInvoiceJournal($invoice, $items);
        }

        $this->setFlash('success', 'Invoice created successfully');
        return $this->redirect(BASE_URL . 'finance/invoice/' . $invoiceId);
    }

    public function viewInvoice($id)
    {
        $invoiceModel = new FinanceInvoiceModel($this->db);
        $itemModel = new FinanceInvoiceItemModel($this->db);
        $paymentModel = new FinancePaymentModel($this->db);
        $coaModel = new FinanceChartOfAccountsModel($this->db);

        $invoice = $invoiceModel->getDetail($id);
        if (!$invoice) {
            $this->setFlash('error', 'Invoice not found');
            return $this->redirect(BASE_URL . 'finance/invoices');
        }

        $data = [
            'title' => 'Invoice ' . $invoice['invoice_no'],
            'currentPage' => 'finance',
            'invoice' => $invoice,
            'items' => $itemModel->getByInvoice($id),
            'payments' => $paymentModel->getByInvoice($id),
            'bank_accounts' => $coaModel->getCashBankAccounts(),
            'flash' => $this->getFlash()
        ];

        return $this->view('finance/invoice_detail', $data);
    }

    public function editInvoice($id)
    {
        $invoiceModel = new FinanceInvoiceModel($this->db);
        $itemModel = new FinanceInvoiceItemModel($this->db);
        $clientModel = new ClientModel($this->db);
        $vesselModel = new VesselModel($this->db);
        $coaModel = new FinanceChartOfAccountsModel($this->db);
        $ccModel = new FinanceCostCenterModel($this->db);

        $invoice = $invoiceModel->find($id);
        if (!$invoice) {
            $this->setFlash('error', 'Invoice not found');
            return $this->redirect(BASE_URL . 'finance/invoices');
        }

        $data = [
            'title' => 'Edit Invoice ' . $invoice['invoice_no'],
            'currentPage' => 'finance',
            'invoice' => $invoice,
            'items' => $itemModel->getByInvoice($id),
            'invoice_no' => $invoice['invoice_no'],
            'clients' => $clientModel->findAll([], 'name ASC'),
            'vessels' => $vesselModel->findAll([], 'name ASC'),
            'revenue_accounts' => $coaModel->getRevenueAccounts(),
            'cost_centers' => $ccModel->getActive(),
            'mode' => 'edit',
            'flash' => $this->getFlash()
        ];

        return $this->view('finance/invoice_form', $data);
    }

    public function updateInvoice($id)
    {
        if (!$this->isPost()) return $this->redirect(BASE_URL . 'finance/invoices');

        $invoiceModel = new FinanceInvoiceModel($this->db);
        $itemModel = new FinanceInvoiceItemModel($this->db);

        $user = $this->getCurrentUser();

        $invoiceData = [
            'client_id' => (int)$this->input('client_id'),
            'vessel_id' => $this->input('vessel_id') ? (int)$this->input('vessel_id') : null,
            'invoice_date' => $this->input('invoice_date'),
            'due_date' => $this->input('due_date'),
            'discount_percent' => floatval($this->input('discount_percent', 0)),
            'tax_percent' => floatval($this->input('tax_percent', 0)),
            'currency_code' => $this->input('currency_code', 'IDR'),
            'exchange_rate' => floatval($this->input('exchange_rate', 1)),
            'cost_center_id' => $this->input('cost_center_id') ? (int)$this->input('cost_center_id') : null,
            'revenue_account_id' => $this->input('revenue_account_id') ? (int)$this->input('revenue_account_id') : null,
            'terms' => $this->input('terms'),
            'notes' => $this->input('notes'),
            'updated_by' => $user['id'] ?? null
        ];

        $invoiceModel->update($id, $invoiceData);

        // Replace items
        $itemModel->deleteByInvoice($id);

        $descriptions = $_POST['item_description'] ?? [];
        $quantities = $_POST['item_quantity'] ?? [];
        $prices = $_POST['item_price'] ?? [];

        foreach ($descriptions as $i => $desc) {
            if (empty($desc)) continue;
            $qty = floatval($quantities[$i] ?? 1);
            $price = floatval($prices[$i] ?? 0);

            $itemModel->insert([
                'invoice_id' => $id,
                'description' => $desc,
                'quantity' => $qty,
                'unit_price' => $price,
                'amount' => round($qty * $price, 2),
                'sort_order' => $i
            ]);
        }

        $invoiceModel->recalculateTotals($id);

        $this->setFlash('success', 'Invoice updated successfully');
        return $this->redirect(BASE_URL . 'finance/invoice/' . $id);
    }

    public function deleteInvoice($id)
    {
        if (!$this->isPost()) return $this->redirect(BASE_URL . 'finance/invoices');

        $invoiceModel = new FinanceInvoiceModel($this->db);
        $invoice = $invoiceModel->find($id);

        if (!$invoice || !in_array($invoice['status'], ['draft', 'cancelled'])) {
            $this->setFlash('error', 'Only draft/cancelled invoices can be deleted');
            return $this->redirect(BASE_URL . 'finance/invoices');
        }

        $invoiceModel->delete($id);
        $this->setFlash('success', 'Invoice deleted');
        return $this->redirect(BASE_URL . 'finance/invoices');
    }

    public function markInvoiceSent($id)
    {
        if (!$this->isPost()) return $this->redirect(BASE_URL . 'finance/invoices');

        $invoiceModel = new FinanceInvoiceModel($this->db);
        $journalModel = new FinanceJournalEntryModel($this->db);
        $itemModel = new FinanceInvoiceItemModel($this->db);

        $invoice = $invoiceModel->find($id);
        if ($invoice && $invoice['status'] === 'draft') {
            $invoiceModel->update($id, [
                'status' => 'unpaid',
                'sent_at' => date('Y-m-d H:i:s')
            ]);

            // Create auto-journal on send
            $invoice = $invoiceModel->find($id);
            $items = $itemModel->getByInvoice($id);
            $journalModel->createInvoiceJournal($invoice, $items);

            $this->setFlash('success', 'Invoice marked as sent');
        }

        return $this->redirect(BASE_URL . 'finance/invoice/' . $id);
    }

    public function cancelInvoice($id)
    {
        if (!$this->isPost()) return $this->redirect(BASE_URL . 'finance/invoices');

        $invoiceModel = new FinanceInvoiceModel($this->db);
        $invoice = $invoiceModel->find($id);

        if ($invoice && in_array($invoice['status'], ['draft', 'unpaid'])) {
            $invoiceModel->update($id, [
                'status' => 'cancelled',
                'cancelled_at' => date('Y-m-d H:i:s')
            ]);
            $this->setFlash('success', 'Invoice cancelled');
        }

        return $this->redirect(BASE_URL . 'finance/invoice/' . $id);
    }

    public function recordInvoicePayment()
    {
        if (!$this->isPost()) return $this->redirect(BASE_URL . 'finance/invoices');

        $invoiceModel = new FinanceInvoiceModel($this->db);
        $paymentModel = new FinancePaymentModel($this->db);
        $journalModel = new FinanceJournalEntryModel($this->db);

        $invoiceId = (int)$this->input('invoice_id');
        $invoice = $invoiceModel->find($invoiceId);
        
        if (!$invoice) {
            $this->setFlash('error', 'Invoice not found');
            return $this->redirect(BASE_URL . 'finance/invoices');
        }

        $user = $this->getCurrentUser();
        $amount = floatval($this->input('amount'));

        $paymentData = [
            'payment_no' => $paymentModel->generatePaymentNo('receivable'),
            'payment_type' => 'receivable',
            'reference_id' => $invoiceId,
            'payment_date' => $this->input('payment_date'),
            'amount' => $amount,
            'payment_method' => $this->input('payment_method', 'bank_transfer'),
            'bank_account_id' => $this->input('bank_account_id') ? (int)$this->input('bank_account_id') : null,
            'reference_number' => $this->input('reference_number'),
            'notes' => $this->input('payment_notes'),
            'created_by' => $user['id'] ?? null
        ];

        $paymentId = $paymentModel->insert($paymentData);

        if ($paymentId) {
            // Create auto-journal for payment
            $payment = $paymentModel->find($paymentId);
            $journalId = $journalModel->createInvoicePaymentJournal($payment, $invoice);

            if ($journalId) {
                $paymentModel->update($paymentId, ['journal_entry_id' => $journalId]);
            }

            // Update invoice payment status
            $invoiceModel->updatePaymentStatus($invoiceId);

            $this->setFlash('success', 'Payment recorded: ' . number_format($amount, 2));
        }

        return $this->redirect(BASE_URL . 'finance/invoice/' . $invoiceId);
    }

    // ═══════════════════════════════════════════
    // BILLS (Accounts Payable)
    // ═══════════════════════════════════════════

    public function bills()
    {
        $billModel = new FinanceBillModel($this->db);

        $filters = [
            'status' => $this->input('status'),
            'category' => $this->input('category'),
            'date_from' => $this->input('date_from'),
            'date_to' => $this->input('date_to'),
        ];

        $data = [
            'title' => 'Bills — Accounts Payable',
            'currentPage' => 'finance',
            'bills' => $billModel->getAllFiltered($filters),
            'filters' => $filters,
            'flash' => $this->getFlash()
        ];

        return $this->view('finance/bills', $data);
    }

    public function createBill()
    {
        $billModel = new FinanceBillModel($this->db);
        $coaModel = new FinanceChartOfAccountsModel($this->db);
        $ccModel = new FinanceCostCenterModel($this->db);

        $data = [
            'title' => 'Create Bill',
            'currentPage' => 'finance',
            'bill_no' => $billModel->generateBillNo(),
            'expense_accounts' => $coaModel->getExpenseAccounts(),
            'cost_centers' => $ccModel->getActive(),
            'mode' => 'create',
            'bill' => null,
            'items' => [],
            'flash' => $this->getFlash()
        ];

        return $this->view('finance/bill_form', $data);
    }

    public function storeBill()
    {
        if (!$this->isPost()) return $this->redirect(BASE_URL . 'finance/bills');

        $billModel = new FinanceBillModel($this->db);
        $itemModel = new FinanceBillItemModel($this->db);
        $journalModel = new FinanceJournalEntryModel($this->db);

        $user = $this->getCurrentUser();

        // Handle receipt upload
        $receiptFile = null;
        if (!empty($_FILES['receipt_file']['name'])) {
            $uploadDir = APPPATH . '../uploads/finance/receipts/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $ext = pathinfo($_FILES['receipt_file']['name'], PATHINFO_EXTENSION);
            $receiptFile = 'receipt_' . date('Ymd_His') . '_' . uniqid() . '.' . $ext;
            move_uploaded_file($_FILES['receipt_file']['tmp_name'], $uploadDir . $receiptFile);
        }

        $billData = [
            'bill_no' => $this->input('bill_no'),
            'vendor_name' => $this->input('vendor_name'),
            'vendor_address' => $this->input('vendor_address'),
            'vendor_phone' => $this->input('vendor_phone'),
            'vendor_email' => $this->input('vendor_email'),
            'bill_date' => $this->input('bill_date'),
            'due_date' => $this->input('due_date'),
            'tax_percent' => floatval($this->input('tax_percent', 0)),
            'currency_code' => $this->input('currency_code', 'IDR'),
            'exchange_rate' => floatval($this->input('exchange_rate', 1)),
            'cost_center_id' => $this->input('cost_center_id') ? (int)$this->input('cost_center_id') : null,
            'expense_account_id' => $this->input('expense_account_id') ? (int)$this->input('expense_account_id') : null,
            'category' => $this->input('category', 'other'),
            'notes' => $this->input('notes'),
            'receipt_file' => $receiptFile,
            'status' => 'unpaid',
            'created_by' => $user['id'] ?? null
        ];

        $billId = $billModel->insert($billData);

        if (!$billId) {
            $this->setFlash('error', 'Failed to create bill');
            return $this->redirect(BASE_URL . 'finance/create-bill');
        }

        // Insert items
        $descriptions = $_POST['item_description'] ?? [];
        $quantities = $_POST['item_quantity'] ?? [];
        $prices = $_POST['item_price'] ?? [];

        foreach ($descriptions as $i => $desc) {
            if (empty($desc)) continue;
            $qty = floatval($quantities[$i] ?? 1);
            $price = floatval($prices[$i] ?? 0);

            $itemModel->insert([
                'bill_id' => $billId,
                'description' => $desc,
                'quantity' => $qty,
                'unit_price' => $price,
                'amount' => round($qty * $price, 2),
                'sort_order' => $i
            ]);
        }

        $billModel->recalculateTotals($billId);

        // Create auto-journal for bill
        $bill = $billModel->find($billId);
        $items = $itemModel->getByBill($billId);
        $journalModel->createBillJournal($bill, $items);

        $this->setFlash('success', 'Bill recorded successfully');
        return $this->redirect(BASE_URL . 'finance/bill/' . $billId);
    }

    public function viewBill($id)
    {
        $billModel = new FinanceBillModel($this->db);
        $itemModel = new FinanceBillItemModel($this->db);
        $paymentModel = new FinancePaymentModel($this->db);
        $coaModel = new FinanceChartOfAccountsModel($this->db);

        $bill = $billModel->getDetail($id);
        if (!$bill) {
            $this->setFlash('error', 'Bill not found');
            return $this->redirect(BASE_URL . 'finance/bills');
        }

        $data = [
            'title' => 'Bill ' . $bill['bill_no'],
            'currentPage' => 'finance',
            'bill' => $bill,
            'items' => $itemModel->getByBill($id),
            'payments' => $paymentModel->getByBill($id),
            'bank_accounts' => $coaModel->getCashBankAccounts(),
            'flash' => $this->getFlash()
        ];

        return $this->view('finance/bill_detail', $data);
    }

    public function editBill($id)
    {
        $billModel = new FinanceBillModel($this->db);
        $itemModel = new FinanceBillItemModel($this->db);
        $coaModel = new FinanceChartOfAccountsModel($this->db);
        $ccModel = new FinanceCostCenterModel($this->db);

        $bill = $billModel->find($id);
        if (!$bill) {
            $this->setFlash('error', 'Bill not found');
            return $this->redirect(BASE_URL . 'finance/bills');
        }

        $data = [
            'title' => 'Edit Bill ' . $bill['bill_no'],
            'currentPage' => 'finance',
            'bill' => $bill,
            'items' => $itemModel->getByBill($id),
            'bill_no' => $bill['bill_no'],
            'expense_accounts' => $coaModel->getExpenseAccounts(),
            'cost_centers' => $ccModel->getActive(),
            'mode' => 'edit',
            'flash' => $this->getFlash()
        ];

        return $this->view('finance/bill_form', $data);
    }

    public function updateBill($id)
    {
        if (!$this->isPost()) return $this->redirect(BASE_URL . 'finance/bills');

        $billModel = new FinanceBillModel($this->db);
        $itemModel = new FinanceBillItemModel($this->db);
        $user = $this->getCurrentUser();

        $billData = [
            'vendor_name' => $this->input('vendor_name'),
            'vendor_address' => $this->input('vendor_address'),
            'vendor_phone' => $this->input('vendor_phone'),
            'vendor_email' => $this->input('vendor_email'),
            'bill_date' => $this->input('bill_date'),
            'due_date' => $this->input('due_date'),
            'tax_percent' => floatval($this->input('tax_percent', 0)),
            'currency_code' => $this->input('currency_code', 'IDR'),
            'cost_center_id' => $this->input('cost_center_id') ? (int)$this->input('cost_center_id') : null,
            'expense_account_id' => $this->input('expense_account_id') ? (int)$this->input('expense_account_id') : null,
            'category' => $this->input('category', 'other'),
            'notes' => $this->input('notes'),
            'updated_by' => $user['id'] ?? null
        ];

        // Handle receipt
        if (!empty($_FILES['receipt_file']['name'])) {
            $uploadDir = APPPATH . '../uploads/finance/receipts/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            $ext = pathinfo($_FILES['receipt_file']['name'], PATHINFO_EXTENSION);
            $billData['receipt_file'] = 'receipt_' . date('Ymd_His') . '_' . uniqid() . '.' . $ext;
            move_uploaded_file($_FILES['receipt_file']['tmp_name'], $uploadDir . $billData['receipt_file']);
        }

        $billModel->update($id, $billData);

        // Replace items
        $itemModel->deleteByBill($id);

        $descriptions = $_POST['item_description'] ?? [];
        $quantities = $_POST['item_quantity'] ?? [];
        $prices = $_POST['item_price'] ?? [];

        foreach ($descriptions as $i => $desc) {
            if (empty($desc)) continue;
            $qty = floatval($quantities[$i] ?? 1);
            $price = floatval($prices[$i] ?? 0);

            $itemModel->insert([
                'bill_id' => $id,
                'description' => $desc,
                'quantity' => $qty,
                'unit_price' => $price,
                'amount' => round($qty * $price, 2),
                'sort_order' => $i
            ]);
        }

        $billModel->recalculateTotals($id);

        $this->setFlash('success', 'Bill updated successfully');
        return $this->redirect(BASE_URL . 'finance/bill/' . $id);
    }

    public function deleteBill($id)
    {
        if (!$this->isPost()) return $this->redirect(BASE_URL . 'finance/bills');

        $billModel = new FinanceBillModel($this->db);
        $bill = $billModel->find($id);

        if (!$bill || !in_array($bill['status'], ['draft', 'cancelled'])) {
            $this->setFlash('error', 'Only draft/cancelled bills can be deleted');
            return $this->redirect(BASE_URL . 'finance/bills');
        }

        $billModel->delete($id);
        $this->setFlash('success', 'Bill deleted');
        return $this->redirect(BASE_URL . 'finance/bills');
    }

    public function recordBillPayment()
    {
        if (!$this->isPost()) return $this->redirect(BASE_URL . 'finance/bills');

        $billModel = new FinanceBillModel($this->db);
        $paymentModel = new FinancePaymentModel($this->db);
        $journalModel = new FinanceJournalEntryModel($this->db);

        $billId = (int)$this->input('bill_id');
        $bill = $billModel->find($billId);

        if (!$bill) {
            $this->setFlash('error', 'Bill not found');
            return $this->redirect(BASE_URL . 'finance/bills');
        }

        $user = $this->getCurrentUser();
        $amount = floatval($this->input('amount'));

        $paymentData = [
            'payment_no' => $paymentModel->generatePaymentNo('payable'),
            'payment_type' => 'payable',
            'reference_id' => $billId,
            'payment_date' => $this->input('payment_date'),
            'amount' => $amount,
            'payment_method' => $this->input('payment_method', 'bank_transfer'),
            'bank_account_id' => $this->input('bank_account_id') ? (int)$this->input('bank_account_id') : null,
            'reference_number' => $this->input('reference_number'),
            'notes' => $this->input('payment_notes'),
            'created_by' => $user['id'] ?? null
        ];

        $paymentId = $paymentModel->insert($paymentData);

        if ($paymentId) {
            $payment = $paymentModel->find($paymentId);
            $journalId = $journalModel->createBillPaymentJournal($payment, $bill);

            if ($journalId) {
                $paymentModel->update($paymentId, ['journal_entry_id' => $journalId]);
            }

            $billModel->updatePaymentStatus($billId);
            $this->setFlash('success', 'Payment recorded: ' . number_format($amount, 2));
        }

        return $this->redirect(BASE_URL . 'finance/bill/' . $billId);
    }

    // ═══════════════════════════════════════════
    // GENERAL LEDGER (Journal Entries)
    // ═══════════════════════════════════════════

    public function journal()
    {
        $journalModel = new FinanceJournalEntryModel($this->db);

        $filters = [
            'date_from' => $this->input('date_from'),
            'date_to' => $this->input('date_to'),
            'source_type' => $this->input('source_type'),
        ];

        $data = [
            'title' => 'General Ledger — Journal Entries',
            'currentPage' => 'finance',
            'entries' => $journalModel->getAllFiltered($filters),
            'filters' => $filters,
            'flash' => $this->getFlash()
        ];

        return $this->view('finance/journal', $data);
    }

    public function viewJournal($id)
    {
        $journalModel = new FinanceJournalEntryModel($this->db);
        $lineModel = new FinanceJournalLineModel($this->db);

        $entry = $journalModel->find($id);
        if (!$entry) {
            $this->setFlash('error', 'Journal entry not found');
            return $this->redirect(BASE_URL . 'finance/journal');
        }

        $data = [
            'title' => 'Journal Entry ' . ($entry['entry_no'] ?? '#' . $id),
            'currentPage' => 'finance',
            'entry' => $entry,
            'lines' => $lineModel->getByEntry($id),
            'flash' => $this->getFlash()
        ];

        return $this->view('finance/journal_detail', $data);
    }

    public function createJournal()
    {
        $coaModel = new FinanceChartOfAccountsModel($this->db);
        $ccModel = new FinanceCostCenterModel($this->db);
        $journalModel = new FinanceJournalEntryModel($this->db);

        $data = [
            'title' => 'Create Manual Journal Entry',
            'currentPage' => 'finance',
            'accounts' => $coaModel->getAll(),
            'cost_centers' => $ccModel->getActive(),
            'entry_no' => $journalModel->generateEntryNo(),
            'flash' => $this->getFlash()
        ];

        return $this->view('finance/journal_form', $data);
    }

    public function storeJournal()
    {
        if (!$this->isPost()) return $this->redirect(BASE_URL . 'finance/journal');

        $journalModel = new FinanceJournalEntryModel($this->db);
        $lineModel = new FinanceJournalLineModel($this->db);

        $user = $this->getCurrentUser();

        // Collect lines
        $accountIds = $_POST['line_account_id'] ?? [];
        $debits = $_POST['line_debit'] ?? [];
        $credits = $_POST['line_credit'] ?? [];
        $ccIds = $_POST['line_cost_center_id'] ?? [];
        $descs = $_POST['line_description'] ?? [];

        $totalDebit = 0;
        $totalCredit = 0;
        $lines = [];

        foreach ($accountIds as $i => $accId) {
            if (empty($accId)) continue;
            $d = floatval($debits[$i] ?? 0);
            $c = floatval($credits[$i] ?? 0);
            if ($d == 0 && $c == 0) continue;

            $totalDebit += $d;
            $totalCredit += $c;

            $lines[] = [
                'account_id' => (int)$accId,
                'cost_center_id' => !empty($ccIds[$i]) ? (int)$ccIds[$i] : null,
                'debit' => round($d, 2),
                'credit' => round($c, 2),
                'description' => $descs[$i] ?? null
            ];
        }

        // Validate balanced entry (international accounting standard)
        if (abs($totalDebit - $totalCredit) > 0.01) {
            $this->setFlash('error', 'Total Debit must equal Total Credit (Debit: ' . number_format($totalDebit, 2) . ', Credit: ' . number_format($totalCredit, 2) . ')');
            return $this->redirect(BASE_URL . 'finance/create-journal');
        }

        if (empty($lines)) {
            $this->setFlash('error', 'At least one journal line is required');
            return $this->redirect(BASE_URL . 'finance/create-journal');
        }

        $entryId = $journalModel->insert([
            'entry_no' => $this->input('entry_no'),
            'entry_date' => $this->input('entry_date'),
            'reference_no' => $this->input('reference_no'),
            'source_type' => 'manual',
            'description' => $this->input('description'),
            'total_debit' => round($totalDebit, 2),
            'total_credit' => round($totalCredit, 2),
            'is_auto' => 0,
            'is_posted' => 1,
            'created_by' => $user['id'] ?? null
        ]);

        if ($entryId) {
            foreach ($lines as $line) {
                $line['journal_entry_id'] = $entryId;
                $lineModel->insert($line);
            }
            $this->setFlash('success', 'Journal entry created successfully');
        }

        return $this->redirect(BASE_URL . 'finance/journal');
    }

    // ═══════════════════════════════════════════
    // CASH FLOW
    // ═══════════════════════════════════════════

    public function cashflow()
    {
        $dashModel = new FinanceDashboardModel($this->db);
        $trend = $dashModel->getMonthlyTrend(12);

        $totalInflow = array_sum(array_column($trend, 'revenue'));
        $totalOutflow = array_sum(array_column($trend, 'expense'));

        $data = [
            'title' => 'Cash Flow Statement',
            'currentPage' => 'finance',
            'trend' => $trend,
            'total_inflow' => $totalInflow,
            'total_outflow' => $totalOutflow,
            'net_cashflow' => $totalInflow - $totalOutflow,
            'flash' => $this->getFlash()
        ];

        return $this->view('finance/cashflow', $data);
    }

    // ═══════════════════════════════════════════
    // PROFIT & LOSS
    // ═══════════════════════════════════════════

    public function profitLoss()
    {
        $pnlModel = new FinancePnLModel($this->db);
        $ccModel = new FinanceCostCenterModel($this->db);

        $startDate = $this->input('start_date', date('Y-01-01'));
        $endDate = $this->input('end_date', date('Y-m-t'));
        $costCenterId = $this->input('cost_center_id');

        $report = $pnlModel->generate($startDate, $endDate, $costCenterId);

        $data = [
            'title' => 'Profit & Loss Statement',
            'currentPage' => 'finance',
            'report' => $report,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'cost_center_id' => $costCenterId,
            'cost_centers' => $ccModel->getActive(),
            'flash' => $this->getFlash()
        ];

        return $this->view('finance/profit_loss', $data);
    }

    // ═══════════════════════════════════════════
    // COST CENTERS
    // ═══════════════════════════════════════════

    public function costCenters()
    {
        $ccModel = new FinanceCostCenterModel($this->db);

        $data = [
            'title' => 'Cost Centers',
            'currentPage' => 'finance',
            'cost_centers' => $ccModel->getDetailedStats(),
            'totals' => $ccModel->getOverallTotals(),
            'flash' => $this->getFlash()
        ];

        return $this->view('finance/cost_centers', $data);
    }

    public function storeCostCenter()
    {
        if (!$this->isPost()) return $this->redirect(BASE_URL . 'finance/cost-centers');

        $ccModel = new FinanceCostCenterModel($this->db);

        $id = $this->input('id');
        $data = [
            'code' => strtoupper($this->input('code')),
            'name' => $this->input('name'),
            'name_en' => $this->input('name_en'),
            'description' => $this->input('description'),
            'is_active' => $this->input('is_active', 1) ? 1 : 0
        ];

        if ($id) {
            $ccModel->update((int)$id, $data);
            $this->setFlash('success', 'Cost center updated');
        } else {
            $ccModel->insert($data);
            $this->setFlash('success', 'Cost center created');
        }

        return $this->redirect(BASE_URL . 'finance/cost-centers');
    }

    public function deleteCostCenter($id)
    {
        if (!$this->isPost()) return $this->redirect(BASE_URL . 'finance/cost-centers');

        $ccModel = new FinanceCostCenterModel($this->db);
        $ccModel->delete((int)$id);
        $this->setFlash('success', 'Cost center deleted');
        return $this->redirect(BASE_URL . 'finance/cost-centers');
    }

    // ═══════════════════════════════════════════
    // CHART OF ACCOUNTS
    // ═══════════════════════════════════════════

    public function accounts()
    {
        $coaModel = new FinanceChartOfAccountsModel($this->db);
        $coaData = $coaModel->getGroupedWithBalances();

        $data = [
            'title' => 'Chart of Accounts',
            'currentPage' => 'finance',
            'accounts' => $coaData['grouped'],
            'totals' => $coaData['totals'],
            'flash' => $this->getFlash()
        ];

        return $this->view('finance/accounts', $data);
    }

    public function storeAccount()
    {
        if (!$this->isPost()) return $this->redirect(BASE_URL . 'finance/accounts');

        $coaModel = new FinanceChartOfAccountsModel($this->db);

        $id = $this->input('id');
        $data = [
            'code' => $this->input('code'),
            'name' => $this->input('name'),
            'name_en' => $this->input('name_en'),
            'type' => $this->input('type'),
            'is_active' => $this->input('is_active', 1) ? 1 : 0
        ];

        if ($id) {
            $coaModel->update((int)$id, $data);
            $this->setFlash('success', 'Account updated');
        } else {
            $coaModel->insert($data);
            $this->setFlash('success', 'Account created');
        }

        return $this->redirect(BASE_URL . 'finance/accounts');
    }

    public function deleteAccount($id)
    {
        if (!$this->isPost()) return $this->redirect(BASE_URL . 'finance/accounts');

        $coaModel = new FinanceChartOfAccountsModel($this->db);
        $account = $coaModel->find((int)$id);

        if ($account && $account['is_system']) {
            $this->setFlash('error', 'System accounts cannot be deleted');
            return $this->redirect(BASE_URL . 'finance/accounts');
        }

        $coaModel->delete((int)$id);
        $this->setFlash('success', 'Account deleted');
        return $this->redirect(BASE_URL . 'finance/accounts');
    }

    /**
     * Invoice PDF - Print-ready invoice
     */
    public function invoicePdf($id)
    {
        $invoiceModel = new FinanceInvoiceModel($this->db);
        $itemModel = new FinanceInvoiceItemModel($this->db);

        $invoice = $invoiceModel->find($id);
        if (!$invoice) {
            $this->setFlash('error', 'Invoice not found');
            return $this->redirect('finance/invoices');
        }

        $data = [
            'invoice' => $invoice,
            'invoiceItems' => $itemModel->getByInvoice($id),
        ];

        return $this->view('finance/invoice_pdf', $data);
    }

    /**
     * P&L Statement PDF
     */
    public function plStatementPdf()
    {
        $month = $this->input('month', date('n'));
        $year = $this->input('year', date('Y'));
        $pnlModel = new FinancePnLModel($this->db);

        $startDate = "$year-" . str_pad($month, 2, '0', STR_PAD_LEFT) . "-01";
        $endDate = date('Y-m-t', strtotime($startDate));

        $pnlData = $pnlModel->generate($startDate, $endDate);

        $data = [
            'totalRevenue' => $pnlData['revenue']['total'] ?? 0,
            'totalExpenses' => ($pnlData['cogs']['total'] ?? 0) + ($pnlData['expenses']['total'] ?? 0),
            'revenueItems' => $pnlData['revenue']['items'] ?? [],
            'expenseItems' => array_merge($pnlData['cogs']['items'] ?? [], $pnlData['expenses']['items'] ?? []),
            'month' => $month,
            'year' => $year,
        ];

        return $this->view('finance/pl_statement_pdf', $data);
    }
}
