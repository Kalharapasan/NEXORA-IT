<?php
$pageTitle = 'Team Management';
require_once __DIR__ . '/includes/header.php';

try {
    $db = getDBConnection();
    
    // Get all team members
    $query = "SELECT * FROM team_members ORDER BY display_order ASC, created_at DESC";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $teamMembers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get team statistics
    $statsQuery = "SELECT 
        COUNT(*) as total_members,
        SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_members,
        SUM(CASE WHEN status = 'inactive' THEN 1 ELSE 0 END) as inactive_members
        FROM team_members";
    $stats = $db->query($statsQuery)->fetch(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    error_log("Team management error: " . $e->getMessage());
    $teamMembers = [];
    $stats = ['total_members' => 0, 'active_members' => 0, 'inactive_members' => 0];
}
?>

<style>
.team-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.team-stat-card {
    background: white;
    padding: 1.5rem;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    text-align: center;
}

.team-stat-card h3 {
    font-size: 2.5rem;
    margin: 0;
    color: #4F46E5;
}

.team-stat-card p {
    margin: 0.5rem 0 0;
    color: #666;
}

.team-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.team-grid-display {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 2rem;
    margin-top: 2rem;
}

.team-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.team-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.15);
}

.team-card-image {
    width: 100%;
    height: 280px;
    overflow: hidden;
    position: relative;
}

.team-card-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.team-status-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
}

.status-active {
    background: #10B981;
    color: white;
}

.status-inactive {
    background: #EF4444;
    color: white;
}

.team-card-content {
    padding: 1.5rem;
}

.team-card-content h3 {
    margin: 0 0 0.5rem;
    font-size: 1.25rem;
    color: #1F2937;
}

.team-card-content .position {
    color: #6B7280;
    font-size: 0.95rem;
    margin-bottom: 1rem;
}

.team-card-content .bio {
    color: #4B5563;
    font-size: 0.9rem;
    line-height: 1.5;
    margin-bottom: 1rem;
    max-height: 60px;
    overflow: hidden;
}

.team-social-links {
    display: flex;
    gap: 0.5rem;
    margin-bottom: 1rem;
}

.team-social-links a {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #F3F4F6;
    color: #6B7280;
    transition: all 0.3s ease;
}

.team-social-links a:hover {
    background: #4F46E5;
    color: white;
}

.team-card-actions {
    display: flex;
    gap: 0.5rem;
    padding-top: 1rem;
    border-top: 1px solid #E5E7EB;
}

.btn-edit, .btn-delete {
    flex: 1;
    padding: 0.5rem;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 0.9rem;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-edit {
    background: #4F46E5;
    color: white;
}

.btn-edit:hover {
    background: #4338CA;
}

.btn-delete {
    background: #EF4444;
    color: white;
}

.btn-delete:hover {
    background: #DC2626;
}

.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.7);
    animation: fadeIn 0.3s ease;
}

.modal-content {
    background: white;
    margin: 3% auto;
    padding: 2rem;
    border-radius: 12px;
    width: 90%;
    max-width: 600px;
    max-height: 85vh;
    overflow-y: auto;
    animation: slideDown 0.3s ease;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slideDown {
    from { transform: translateY(-50px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}

.modal-header h2 {
    margin: 0;
    color: #1F2937;
}

.close-modal {
    font-size: 2rem;
    color: #9CA3AF;
    cursor: pointer;
    border: none;
    background: none;
}

.close-modal:hover {
    color: #EF4444;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
    color: #374151;
}

.form-group input,
.form-group textarea,
.form-group select {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #D1D5DB;
    border-radius: 6px;
    font-size: 1rem;
    transition: border-color 0.3s ease;
}

.form-group input:focus,
.form-group textarea:focus,
.form-group select:focus {
    outline: none;
    border-color: #4F46E5;
}

.form-group textarea {
    resize: vertical;
    min-height: 100px;
}

.form-actions {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
    margin-top: 2rem;
}

.btn-cancel {
    padding: 0.75rem 1.5rem;
    background: #E5E7EB;
    color: #374151;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 500;
    transition: background 0.3s ease;
}

.btn-cancel:hover {
    background: #D1D5DB;
}

.btn-save {
    padding: 0.75rem 1.5rem;
    background: #10B981;
    color: white;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 500;
    transition: background 0.3s ease;
}

.btn-save:hover {
    background: #059669;
}

.alert {
    padding: 1rem;
    margin-bottom: 1.5rem;
    border-radius: 8px;
    display: none;
}

.alert-success {
    background: #D1FAE5;
    color: #065F46;
    border: 1px solid #10B981;
}

.alert-error {
    background: #FEE2E2;
    color: #991B1B;
    border: 1px solid #EF4444;
}

.image-preview {
    margin-top: 1rem;
    max-width: 100%;
}

.image-preview img {
    max-width: 100%;
    height: auto;
    border-radius: 8px;
    border: 2px solid #E5E7EB;
}
</style>

<div class="alert alert-success" id="successAlert"></div>
<div class="alert alert-error" id="errorAlert"></div>

<div class="team-stats">
    <div class="team-stat-card">
        <h3><?php echo $stats['total_members']; ?></h3>
        <p>Total Members</p>
    </div>
    <div class="team-stat-card">
        <h3><?php echo $stats['active_members']; ?></h3>
        <p>Active Members</p>
    </div>
    <div class="team-stat-card">
        <h3><?php echo $stats['inactive_members']; ?></h3>
        <p>Inactive Members</p>
    </div>
</div>

<div class="team-header">
    <h1>Team Members Management</h1>
    <button class="btn btn-primary" onclick="openAddModal()">
        <i class="fas fa-plus"></i> Add Team Member
    </button>
</div>

<div class="team-grid-display">
    <?php foreach ($teamMembers as $member): ?>
        <div class="team-card" data-id="<?php echo $member['id']; ?>">
            <div class="team-card-image">
                <?php if ($member['image_url']): ?>
                    <img src="<?php echo htmlspecialchars($member['image_url']); ?>" alt="<?php echo htmlspecialchars($member['name']); ?>">
                <?php else: ?>
                    <img src="https://via.placeholder.com/400x400?text=No+Image" alt="No Image">
                <?php endif; ?>
                <span class="team-status-badge status-<?php echo $member['status']; ?>">
                    <?php echo ucfirst($member['status']); ?>
                </span>
            </div>
            <div class="team-card-content">
                <h3><?php echo htmlspecialchars($member['name']); ?></h3>
                <p class="position"><?php echo htmlspecialchars($member['position']); ?></p>
                <?php if ($member['bio']): ?>
                    <p class="bio"><?php echo htmlspecialchars($member['bio']); ?></p>
                <?php endif; ?>
                
                <div class="team-social-links">
                    <?php if ($member['linkedin_url']): ?>
                        <a href="<?php echo htmlspecialchars($member['linkedin_url']); ?>" target="_blank" title="LinkedIn">
                            <i class="fab fa-linkedin"></i>
                        </a>
                    <?php endif; ?>
                    <?php if ($member['twitter_url']): ?>
                        <a href="<?php echo htmlspecialchars($member['twitter_url']); ?>" target="_blank" title="Twitter">
                            <i class="fab fa-twitter"></i>
                        </a>
                    <?php endif; ?>
                    <?php if ($member['github_url']): ?>
                        <a href="<?php echo htmlspecialchars($member['github_url']); ?>" target="_blank" title="GitHub">
                            <i class="fab fa-github"></i>
                        </a>
                    <?php endif; ?>
                </div>
                
                <div class="team-card-actions">
                    <button class="btn-edit" onclick="openEditModal(<?php echo $member['id']; ?>)">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <button class="btn-delete" onclick="deleteMember(<?php echo $member['id']; ?>)">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Add/Edit Modal -->
<div id="teamModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="modalTitle">Add Team Member</h2>
            <button class="close-modal" onclick="closeModal()">&times;</button>
        </div>
        
        <form id="teamForm">
            <input type="hidden" id="memberId" name="memberId">
            
            <div class="form-group">
                <label for="name">Name *</label>
                <input type="text" id="name" name="name" required>
            </div>
            
            <div class="form-group">
                <label for="position">Position *</label>
                <input type="text" id="position" name="position" required>
            </div>
            
            <div class="form-group">
                <label for="bio">Bio</label>
                <textarea id="bio" name="bio" placeholder="Brief description about the team member..."></textarea>
            </div>
            
            <div class="form-group">
                <label for="imageUrl">Image URL *</label>
                <input type="url" id="imageUrl" name="imageUrl" required placeholder="https://example.com/image.jpg">
                <div class="image-preview" id="imagePreview"></div>
            </div>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="member@example.com">
            </div>
            
            <div class="form-group">
                <label for="phone">Phone</label>
                <input type="tel" id="phone" name="phone" placeholder="+1234567890">
            </div>
            
            <div class="form-group">
                <label for="linkedinUrl">LinkedIn URL</label>
                <input type="url" id="linkedinUrl" name="linkedinUrl" placeholder="https://linkedin.com/in/username">
            </div>
            
            <div class="form-group">
                <label for="twitterUrl">Twitter URL</label>
                <input type="url" id="twitterUrl" name="twitterUrl" placeholder="https://twitter.com/username">
            </div>
            
            <div class="form-group">
                <label for="githubUrl">GitHub URL</label>
                <input type="url" id="githubUrl" name="githubUrl" placeholder="https://github.com/username">
            </div>
            
            <div class="form-group">
                <label for="displayOrder">Display Order</label>
                <input type="number" id="displayOrder" name="displayOrder" value="0" min="0">
            </div>
            
            <div class="form-group">
                <label for="status">Status</label>
                <select id="status" name="status">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            
            <div class="form-actions">
                <button type="button" class="btn-cancel" onclick="closeModal()">Cancel</button>
                <button type="submit" class="btn-save">
                    <i class="fas fa-save"></i> Save
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Image preview
document.getElementById('imageUrl').addEventListener('input', function() {
    const preview = document.getElementById('imagePreview');
    const url = this.value.trim();
    
    if (url) {
        preview.innerHTML = `<img src="${url}" alt="Preview" onerror="this.src='https://via.placeholder.com/400x400?text=Invalid+URL'">`;
    } else {
        preview.innerHTML = '';
    }
});

function openAddModal() {
    document.getElementById('modalTitle').textContent = 'Add Team Member';
    document.getElementById('teamForm').reset();
    document.getElementById('memberId').value = '';
    document.getElementById('imagePreview').innerHTML = '';
    document.getElementById('teamModal').style.display = 'block';
}

function openEditModal(id) {
    fetch(`ajax/team_operations.php?action=get&id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const member = data.member;
                document.getElementById('modalTitle').textContent = 'Edit Team Member';
                document.getElementById('memberId').value = member.id;
                document.getElementById('name').value = member.name;
                document.getElementById('position').value = member.position;
                document.getElementById('bio').value = member.bio || '';
                document.getElementById('imageUrl').value = member.image_url || '';
                document.getElementById('email').value = member.email || '';
                document.getElementById('phone').value = member.phone || '';
                document.getElementById('linkedinUrl').value = member.linkedin_url || '';
                document.getElementById('twitterUrl').value = member.twitter_url || '';
                document.getElementById('githubUrl').value = member.github_url || '';
                document.getElementById('displayOrder').value = member.display_order;
                document.getElementById('status').value = member.status;
                
                // Show image preview
                if (member.image_url) {
                    document.getElementById('imagePreview').innerHTML = `<img src="${member.image_url}" alt="Preview">`;
                }
                
                document.getElementById('teamModal').style.display = 'block';
            } else {
                showAlert('Error loading member data', 'error');
            }
        })
        .catch(error => {
            // console.error('Error:', error);
            showAlert('Error loading member data', 'error');
        });
}

function closeModal() {
    document.getElementById('teamModal').style.display = 'none';
    document.getElementById('teamForm').reset();
    document.getElementById('imagePreview').innerHTML = '';
}

function deleteMember(id) {
    if (!confirm('Are you sure you want to delete this team member?')) {
        return;
    }
    
    fetch('ajax/team_operations.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            action: 'delete',
            id: id
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Team member deleted successfully!', 'success');
            setTimeout(() => location.reload(), 1500);
        } else {
            showAlert(data.message || 'Error deleting team member', 'error');
        }
    })
    .catch(error => {
        // console.error('Error:', error);
        showAlert('Error deleting team member', 'error');
    });
}

document.getElementById('teamForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const memberId = document.getElementById('memberId').value;
    
    const data = {
        action: memberId ? 'update' : 'add',
        id: memberId,
        name: formData.get('name'),
        position: formData.get('position'),
        bio: formData.get('bio'),
        imageUrl: formData.get('imageUrl'),
        email: formData.get('email'),
        phone: formData.get('phone'),
        linkedinUrl: formData.get('linkedinUrl'),
        twitterUrl: formData.get('twitterUrl'),
        githubUrl: formData.get('githubUrl'),
        displayOrder: formData.get('displayOrder'),
        status: formData.get('status')
    };
    
    fetch('ajax/team_operations.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert(`Team member ${memberId ? 'updated' : 'added'} successfully!`, 'success');
            closeModal();
            setTimeout(() => location.reload(), 1500);
        } else {
            showAlert(data.message || 'Error saving team member', 'error');
        }
    })
    .catch(error => {
        // console.error('Error:', error);
        showAlert('Error saving team member', 'error');
    });
});

function showAlert(message, type) {
    const alertId = type === 'success' ? 'successAlert' : 'errorAlert';
    const alert = document.getElementById(alertId);
    alert.textContent = message;
    alert.style.display = 'block';
    
    setTimeout(() => {
        alert.style.display = 'none';
    }, 5000);
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('teamModal');
    if (event.target === modal) {
        closeModal();
    }
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
