<?php
$pageTitle = 'Email Templates';
require_once __DIR__ . '/includes/header.php';

// Check if user has permission (at least admin role)
if (!isset($_SESSION['admin_role']) || !in_array($_SESSION['admin_role'], ['admin', 'super_admin'])) {
    header('Location: dashboard.php');
    exit;
}

try {
    $db = getDBConnection();
    
    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['action'])) {
            $action = $_POST['action'];
            
            if ($action === 'add' || $action === 'edit') {
                $id = $_POST['id'] ?? null;
                $name = trim($_POST['name']);
                $subject = trim($_POST['subject']);
                $body = trim($_POST['body']);
                $templateType = $_POST['template_type'];
                $variables = $_POST['variables'] ?? '';
                $isActive = isset($_POST['is_active']) ? 1 : 0;
                
                if ($action === 'add') {
                    $stmt = $db->prepare("INSERT INTO email_templates (name, subject, body, template_type, variables, is_active, created_by) VALUES (?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$name, $subject, $body, $templateType, $variables, $isActive, $_SESSION['admin_id']]);
                    $message = "Template added successfully!";
                } else {
                    $stmt = $db->prepare("UPDATE email_templates SET name = ?, subject = ?, body = ?, template_type = ?, variables = ?, is_active = ? WHERE id = ?");
                    $stmt->execute([$name, $subject, $body, $templateType, $variables, $isActive, $id]);
                    $message = "Template updated successfully!";
                }
                
                // Log activity
                logAdminActivity($_SESSION['admin_id'], 'email_template_' . $action, "Email template: $name");
            }
            
            if ($action === 'delete') {
                $id = $_POST['id'];
                $stmt = $db->prepare("DELETE FROM email_templates WHERE id = ?");
                $stmt->execute([$id]);
                $message = "Template deleted successfully!";
                
                logAdminActivity($_SESSION['admin_id'], 'email_template_delete', "Template ID: $id");
            }
            
            if ($action === 'toggle_status') {
                $id = $_POST['id'];
                $stmt = $db->prepare("UPDATE email_templates SET is_active = NOT is_active WHERE id = ?");
                $stmt->execute([$id]);
                $message = "Template status updated!";
            }
        }
    }
    
    // Get all templates
    $templatesQuery = "SELECT et.*, au.username as creator_name 
                      FROM email_templates et 
                      LEFT JOIN admin_users au ON et.created_by = au.id 
                      ORDER BY et.template_type, et.name";
    $templates = $db->query($templatesQuery)->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    error_log("Email templates error: " . $e->getMessage());
    $error = "An error occurred. Please try again.";
    $templates = [];
}
?>

<div class="content-header">
    <h1><i class="fas fa-envelope-open-text"></i> Email Templates</h1>
    <button class="btn btn-primary" onclick="showAddTemplateModal()">
        <i class="fas fa-plus"></i> Add Template
    </button>
</div>

<?php if (isset($message)): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
<?php endif; ?>

<?php if (isset($error)): ?>
    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h2>Email Templates</h2>
        <p class="subtitle">Manage email templates for automated communications</p>
    </div>
    
    <div class="card-body">
        <?php if (empty($templates)): ?>
            <div class="empty-state">
                <i class="fas fa-envelope-open-text"></i>
                <p>No email templates found. Create your first template!</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Subject</th>
                            <th>Variables</th>
                            <th>Status</th>
                            <th>Created By</th>
                            <th>Last Updated</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($templates as $template): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($template['name']); ?></strong></td>
                                <td>
                                    <span class="badge badge-info">
                                        <?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $template['template_type']))); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars(substr($template['subject'], 0, 50)) . (strlen($template['subject']) > 50 ? '...' : ''); ?></td>
                                <td>
                                    <?php 
                                    $vars = json_decode($template['variables'], true);
                                    if ($vars) {
                                        echo '<code class="small">' . implode(', ', $vars) . '</code>';
                                    } else {
                                        echo '-';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="action" value="toggle_status">
                                        <input type="hidden" name="id" value="<?php echo $template['id']; ?>">
                                        <button type="submit" class="btn-link status-badge <?php echo $template['is_active'] ? 'active' : 'inactive'; ?>">
                                            <?php echo $template['is_active'] ? 'Active' : 'Inactive'; ?>
                                        </button>
                                    </form>
                                </td>
                                <td><?php echo htmlspecialchars($template['creator_name'] ?? 'System'); ?></td>
                                <td><?php echo date('M d, Y', strtotime($template['updated_at'] ?? $template['created_at'])); ?></td>
                                <td class="actions">
                                    <button class="btn-icon" onclick="viewTemplate(<?php echo htmlspecialchars(json_encode($template)); ?>)" title="View">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn-icon" onclick="editTemplate(<?php echo htmlspecialchars(json_encode($template)); ?>)" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form method="POST" style="display: inline;" onsubmit="return confirm('Delete this template?');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?php echo $template['id']; ?>">
                                        <button type="submit" class="btn-icon btn-danger" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Add/Edit Template Modal -->
<div id="templateModal" class="modal">
    <div class="modal-content modal-large">
        <div class="modal-header">
            <h2 id="modalTitle">Add Email Template</h2>
            <button class="modal-close" onclick="closeModal()">&times;</button>
        </div>
        <form id="templateForm" method="POST">
            <input type="hidden" name="action" id="formAction" value="add">
            <input type="hidden" name="id" id="templateId">
            
            <div class="modal-body">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Template Name *</label>
                        <input type="text" name="name" id="templateName" required class="form-control">
                    </div>
                    
                    <div class="form-group">
                        <label>Template Type *</label>
                        <select name="template_type" id="templateType" required class="form-control">
                            <option value="custom">Custom</option>
                            <option value="contact_reply">Contact Reply</option>
                            <option value="newsletter">Newsletter</option>
                            <option value="welcome">Welcome</option>
                            <option value="notification">Notification</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Email Subject *</label>
                    <input type="text" name="subject" id="templateSubject" required class="form-control">
                    <small class="form-hint">Use {{variable}} for dynamic content</small>
                </div>
                
                <div class="form-group">
                    <label>Email Body *</label>
                    <textarea name="body" id="templateBody" required class="form-control" rows="10"></textarea>
                    <small class="form-hint">Use {{variable}} for dynamic content. Available variables: {{name}}, {{email}}, {{subject}}, {{message}}, {{custom_message}}</small>
                </div>
                
                <div class="form-group">
                    <label>Variables (comma-separated)</label>
                    <input type="text" name="variables" id="templateVariables" class="form-control" placeholder="name,email,subject">
                    <small class="form-hint">List variables that can be used in this template</small>
                </div>
                
                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="is_active" id="templateActive" checked>
                        Active
                    </label>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Template</button>
            </div>
        </form>
    </div>
</div>

<!-- View Template Modal -->
<div id="viewModal" class="modal">
    <div class="modal-content modal-large">
        <div class="modal-header">
            <h2>Template Preview</h2>
            <button class="modal-close" onclick="closeViewModal()">&times;</button>
        </div>
        <div class="modal-body">
            <div class="template-preview">
                <div class="preview-section">
                    <strong>Subject:</strong>
                    <p id="previewSubject"></p>
                </div>
                <div class="preview-section">
                    <strong>Body:</strong>
                    <pre id="previewBody"></pre>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeViewModal()">Close</button>
        </div>
    </div>
</div>

<script>
function showAddTemplateModal() {
    document.getElementById('modalTitle').textContent = 'Add Email Template';
    document.getElementById('formAction').value = 'add';
    document.getElementById('templateForm').reset();
    document.getElementById('templateId').value = '';
    document.getElementById('templateActive').checked = true;
    document.getElementById('templateModal').style.display = 'flex';
}

function editTemplate(template) {
    document.getElementById('modalTitle').textContent = 'Edit Email Template';
    document.getElementById('formAction').value = 'edit';
    document.getElementById('templateId').value = template.id;
    document.getElementById('templateName').value = template.name;
    document.getElementById('templateType').value = template.template_type;
    document.getElementById('templateSubject').value = template.subject;
    document.getElementById('templateBody').value = template.body;
    document.getElementById('templateVariables').value = template.variables ? JSON.parse(template.variables).join(',') : '';
    document.getElementById('templateActive').checked = template.is_active == 1;
    document.getElementById('templateModal').style.display = 'flex';
}

function viewTemplate(template) {
    document.getElementById('previewSubject').textContent = template.subject;
    document.getElementById('previewBody').textContent = template.body;
    document.getElementById('viewModal').style.display = 'flex';
}

function closeModal() {
    document.getElementById('templateModal').style.display = 'none';
}

function closeViewModal() {
    document.getElementById('viewModal').style.display = 'none';
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('templateModal');
    const viewModal = document.getElementById('viewModal');
    if (event.target === modal) {
        closeModal();
    }
    if (event.target === viewModal) {
        closeViewModal();
    }
}
</script>

<style>
.form-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
}

.form-hint {
    display: block;
    margin-top: 0.25rem;
    font-size: 0.875rem;
    color: #666;
}

.template-preview {
    background: #f8f9fa;
    padding: 1.5rem;
    border-radius: 8px;
}

.preview-section {
    margin-bottom: 1.5rem;
}

.preview-section strong {
    display: block;
    margin-bottom: 0.5rem;
    color: #333;
}

.preview-section pre {
    background: white;
    padding: 1rem;
    border-radius: 4px;
    border: 1px solid #ddd;
    white-space: pre-wrap;
    word-wrap: break-word;
}

.status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
    font-size: 0.875rem;
    font-weight: 500;
    border: none;
    cursor: pointer;
}

.status-badge.active {
    background: #d4edda;
    color: #155724;
}

.status-badge.inactive {
    background: #f8d7da;
    color: #721c24;
}

.btn-link {
    background: none;
    border: none;
    padding: 0;
    cursor: pointer;
}

code.small {
    font-size: 0.875rem;
    background: #f0f0f0;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
}
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
