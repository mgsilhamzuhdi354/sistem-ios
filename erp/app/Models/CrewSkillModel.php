<?php
/**
 * PT Indo Ocean - ERP System
 * Crew Skill Model - Manage crew skills and competencies
 */

namespace App\Models;

class CrewSkillModel extends BaseModel
{
    protected $table = 'crew_skills';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'crew_id',
        'skill_name',
        'skill_level',
        'certificate_id',
        'notes'
    ];

    /**
     * Get all skills for a specific crew member
     */
    public function getByCrew($crewId)
    {
        $sql = "SELECT cs.*, c.full_name as crew_name, c.status as crew_status
                FROM {$this->table} cs
                LEFT JOIN crews c ON cs.crew_id = c.id
                WHERE cs.crew_id = ?
                ORDER BY cs.skill_name ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $crewId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get all skills across all crew members
     */
    public function getAll()
    {
        $sql = "SELECT cs.*, c.full_name as crew_name, c.status as crew_status
                FROM {$this->table} cs
                LEFT JOIN crews c ON cs.crew_id = c.id
                ORDER BY cs.skill_name ASC, c.full_name ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get skill matrix - skills grouped by skill name
     * Returns array with skill_name as key, array of crew members as value
     */
    public function getSkillMatrix()
    {
        $sql = "SELECT cs.*, c.full_name as crew_name, c.status as crew_status, c.employee_id
                FROM {$this->table} cs
                INNER JOIN crews c ON cs.crew_id = c.id
                ORDER BY cs.skill_name ASC, cs.skill_level DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        $skills = $result->fetch_all(MYSQLI_ASSOC);

        // Group by skill name
        $matrix = [];
        foreach ($skills as $skill) {
            $skillName = $skill['skill_name'];
            if (!isset($matrix[$skillName])) {
                $matrix[$skillName] = [];
            }
            $matrix[$skillName][] = $skill;
        }

        return $matrix;
    }

    /**
     * Get list of unique skill names (for dropdown/autocomplete)
     */
    public function getUniqueSkillNames()
    {
        $sql = "SELECT DISTINCT skill_name 
                FROM {$this->table} 
                ORDER BY skill_name ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        $results = $result->fetch_all(MYSQLI_ASSOC);

        return array_column($results, 'skill_name');
    }

    /**
     * Get skill statistics
     */
    public function getStatistics()
    {
        $sql = "SELECT 
                    COUNT(DISTINCT crew_id) as total_crew_with_skills,
                    COUNT(DISTINCT skill_name) as total_unique_skills,
                    COUNT(*) as total_skill_entries,
                    SUM(CASE WHEN skill_level = 'basic' THEN 1 ELSE 0 END) as basic_count,
                    SUM(CASE WHEN skill_level = 'intermediate' THEN 1 ELSE 0 END) as intermediate_count,
                    SUM(CASE WHEN skill_level = 'advanced' THEN 1 ELSE 0 END) as advanced_count,
                    SUM(CASE WHEN skill_level = 'expert' THEN 1 ELSE 0 END) as expert_count
                FROM {$this->table}";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    /**
     * Get crew members by skill name and optional level filter
     */
    public function getCrewBySkill($skillName, $minLevel = null)
    {
        $levelOrder = ['basic' => 1, 'intermediate' => 2, 'advanced' => 3, 'expert' => 4];

        $sql = "SELECT cs.*, c.full_name as crew_name, c.status as crew_status, c.employee_id
                FROM {$this->table} cs
                INNER JOIN crews c ON cs.crew_id = c.id
                WHERE cs.skill_name = ?";

        $params = [$skillName];
        $types = 's';

        if ($minLevel && isset($levelOrder[$minLevel])) {
            $sql .= " AND FIELD(cs.skill_level, 'basic', 'intermediate', 'advanced', 'expert') >= ?";
            $params[] = $levelOrder[$minLevel];
            $types .= 'i';
        }

        $sql .= " ORDER BY FIELD(cs.skill_level, 'expert', 'advanced', 'intermediate', 'basic')";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Check if crew already has a specific skill
     */
    public function hasSkill($crewId, $skillName)
    {
        $sql = "SELECT id FROM {$this->table} 
                WHERE crew_id = ? AND skill_name = ?";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('is', $crewId, $skillName);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc() !== null;
    }

    /**
     * Delete all skills for a crew member
     */
    public function deleteByCrew($crewId)
    {
        $sql = "DELETE FROM {$this->table} WHERE crew_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $crewId);
        return $stmt->execute();
    }
}
