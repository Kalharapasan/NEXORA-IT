-- ============================================
-- VERIFICATION & TESTING SCRIPT
-- Run this after database setup to verify everything is working
-- ============================================

USE nexora_db;

-- ============================================
-- 1. CHECK ALL TABLES EXIST
-- ============================================

SELECT 'Checking Tables...' AS Step;

SELECT 
    TABLE_NAME,
    TABLE_ROWS,
    ROUND(((DATA_LENGTH + INDEX_LENGTH) / 1024 / 1024), 2) AS 'Size_MB'
FROM information_schema.TABLES
WHERE TABLE_SCHEMA = 'nexora_db'
AND TABLE_NAME IN ('team_members', 'contact_messages', 'newsletter_subscribers', 'admin_users')
ORDER BY TABLE_NAME;

-- ============================================
-- 2. VERIFY TEAM MEMBERS TABLE STRUCTURE
-- ============================================

SELECT 'Verifying team_members structure...' AS Step;
DESCRIBE team_members;

-- ============================================
-- 3. CHECK SAMPLE DATA
-- ============================================

SELECT 'Checking sample team members...' AS Step;

SELECT 
    id,
    name,
    position,
    status,
    display_order,
    created_at
FROM team_members
ORDER BY display_order;

-- Expected: 4 rows

-- ============================================
-- 4. VERIFY INDEXES
-- ============================================

SELECT 'Checking indexes...' AS Step;

SHOW INDEX FROM team_members;

-- Should show:
-- PRIMARY (id)
-- idx_status (status)
-- idx_display_order (display_order)

-- ============================================
-- 5. TEST TEAM STATS VIEW
-- ============================================

SELECT 'Testing team_stats view...' AS Step;

SELECT * FROM team_stats;

-- Expected output:
-- total_members: 4
-- active_members: 4
-- inactive_members: 0

-- ============================================
-- 6. TEST ACTIVE MEMBERS QUERY (Used by Frontend)
-- ============================================

SELECT 'Testing active members query...' AS Step;

SELECT 
    id,
    name,
    position,
    image_url,
    linkedin_url,
    twitter_url,
    github_url,
    display_order
FROM team_members 
WHERE status = 'active' 
ORDER BY display_order ASC;

-- Should return 4 active members in order

-- ============================================
-- 7. TEST ADMIN USER EXISTS
-- ============================================

SELECT 'Checking admin user...' AS Step;

SELECT 
    username,
    email,
    full_name,
    role,
    is_active
FROM admin_users
WHERE username = 'admin';

-- Should return 1 row with username: admin

-- ============================================
-- 8. TEST INSERT (Add Test Member)
-- ============================================

SELECT 'Testing INSERT operation...' AS Step;

INSERT INTO team_members 
(name, position, bio, image_url, display_order, status, created_at)
VALUES 
('Test Member', 'Test Position', 'This is a test member for verification', 
 'https://via.placeholder.com/400x400?text=Test', 99, 'inactive', NOW());

SELECT 'Test member inserted successfully' AS Result;

-- ============================================
-- 9. TEST UPDATE
-- ============================================

SELECT 'Testing UPDATE operation...' AS Step;

UPDATE team_members 
SET bio = 'Updated test member bio',
    position = 'Updated Test Position'
WHERE name = 'Test Member';

SELECT 'Test member updated successfully' AS Result;

-- ============================================
-- 10. TEST SELECT
-- ============================================

SELECT 'Testing SELECT operation...' AS Step;

SELECT * FROM team_members WHERE name = 'Test Member';

-- Should show updated bio and position

-- ============================================
-- 11. TEST DELETE
-- ============================================

SELECT 'Testing DELETE operation...' AS Step;

DELETE FROM team_members WHERE name = 'Test Member';

SELECT 'Test member deleted successfully' AS Result;

-- ============================================
-- 12. VERIFY DASHBOARD STATS VIEW
-- ============================================

SELECT 'Testing dashboard_stats view...' AS Step;

SELECT * FROM dashboard_stats;

-- Should show counts for messages and subscribers

-- ============================================
-- 13. CHECK ALL VIEWS
-- ============================================

SELECT 'Checking all views...' AS Step;

SELECT 
    TABLE_NAME,
    VIEW_DEFINITION
FROM information_schema.VIEWS
WHERE TABLE_SCHEMA = 'nexora_db'
ORDER BY TABLE_NAME;

-- Should include:
-- dashboard_stats
-- newsletter_stats
-- recent_contact_messages
-- team_stats

-- ============================================
-- 14. PERFORMANCE TEST (Optional)
-- ============================================

SELECT 'Running performance test...' AS Step;

-- Test query with EXPLAIN
EXPLAIN SELECT * FROM team_members WHERE status = 'active' ORDER BY display_order;

-- Should use indexes (check key column shows idx_status or idx_display_order)

-- ============================================
-- 15. FINAL STATISTICS
-- ============================================

SELECT 'Final Statistics' AS Step;

SELECT 
    'Total Tables' AS Metric,
    COUNT(*) AS Value
FROM information_schema.TABLES
WHERE TABLE_SCHEMA = 'nexora_db'
UNION ALL
SELECT 
    'Team Members',
    COUNT(*) 
FROM team_members
UNION ALL
SELECT 
    'Active Members',
    COUNT(*) 
FROM team_members 
WHERE status = 'active'
UNION ALL
SELECT 
    'Inactive Members',
    COUNT(*) 
FROM team_members 
WHERE status = 'inactive'
UNION ALL
SELECT 
    'Admin Users',
    COUNT(*) 
FROM admin_users
UNION ALL
SELECT 
    'Contact Messages',
    COUNT(*) 
FROM contact_messages
UNION ALL
SELECT 
    'Newsletter Subscribers',
    COUNT(*) 
FROM newsletter_subscribers;

-- ============================================
-- 16. FINAL VERIFICATION MESSAGE
-- ============================================

SELECT '✅ ALL TESTS COMPLETED!' AS Status;

SELECT 
    CASE 
        WHEN (SELECT COUNT(*) FROM team_members) >= 4 THEN '✅ Team members table populated'
        ELSE '❌ Team members table is empty'
    END AS 'Team Members Check',
    CASE 
        WHEN (SELECT COUNT(*) FROM admin_users WHERE username = 'admin') = 1 THEN '✅ Admin user exists'
        ELSE '❌ Admin user not found'
    END AS 'Admin User Check',
    CASE 
        WHEN (SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA = 'nexora_db' AND TABLE_NAME = 'team_members') = 1 THEN '✅ Team members table exists'
        ELSE '❌ Team members table missing'
    END AS 'Table Check',
    CASE 
        WHEN (SELECT COUNT(*) FROM information_schema.VIEWS WHERE TABLE_SCHEMA = 'nexora_db' AND TABLE_NAME = 'team_stats') = 1 THEN '✅ Team stats view exists'
        ELSE '❌ Team stats view missing'
    END AS 'View Check';

-- ============================================
-- QUICK REFERENCE QUERIES
-- ============================================

SELECT '
========================================
QUICK REFERENCE QUERIES
========================================

-- View all team members:
SELECT * FROM team_members ORDER BY display_order;

-- Count active members:
SELECT COUNT(*) FROM team_members WHERE status = "active";

-- View team statistics:
SELECT * FROM team_stats;

-- Add new member:
INSERT INTO team_members (name, position, image_url, display_order, status, created_at)
VALUES ("Name", "Position", "https://...", 1, "active", NOW());

-- Update member:
UPDATE team_members SET position = "New Position" WHERE id = 1;

-- Delete member:
DELETE FROM team_members WHERE id = 1;

-- Set member inactive:
UPDATE team_members SET status = "inactive" WHERE id = 1;

-- Reorder members:
UPDATE team_members SET display_order = 1 WHERE id = 1;

-- Delete all sample data:
DELETE FROM team_members WHERE id <= 4;

========================================
' AS 'Quick Reference';

-- ============================================
-- END OF VERIFICATION SCRIPT
-- ============================================

SELECT 'Verification Complete! Check results above.' AS Message;
SELECT 'If all checks show ✅, your system is ready!' AS Next_Step;
SELECT 'Access admin panel at: http://localhost/admin/' AS Admin_URL;
SELECT 'View website at: http://localhost/index.html' AS Website_URL;
