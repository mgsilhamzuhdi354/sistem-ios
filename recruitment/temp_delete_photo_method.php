    /**
     * Delete profile photo
     */
    public function deletePhoto() {
        if (!$this->isPost()) {
            redirect(url('/crewing/settings?tab=profile'));
        }
        
        $userId = $_SESSION['user_id'];
        
        // Get current photo
        $stmt = $this->db->prepare("SELECT photo FROM crewing_profiles WHERE user_id = ?");
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        
        if ($result && !empty($result['photo'])) {
            // Delete file
            $photoPath = FCPATH . 'uploads/recruiters/' . $result['photo'];
            if (file_exists($photoPath)) {
                unlink($photoPath);
            }
            
            // Update database
            $updateStmt = $this->db->prepare("UPDATE crewing_profiles SET photo = NULL WHERE user_id = ?");
            $updateStmt->bind_param('i', $userId);
            $updateStmt->execute();
            
            flash('success', 'Photo deleted successfully!');
        } else {
            flash('error', 'No photo to delete');
        }
        
        redirect(url('/crewing/settings?tab=profile'));
    }
