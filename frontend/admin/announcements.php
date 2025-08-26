<?php
require_once __DIR__ . "/../../backend/models/Announcement.php";

// Fetch announcements
$announcements = Announcement::getAll(); // Assuming you have this method
?>

<section class="announcements-management">
    <h2>Announcements Management <i class="fas fa-bullhorn"></i></h2>
    <p>Create and manage announcements for field workers by department</p>

    <div class="controls-container">
        <div class="controls">
            <button class="add-btn" onclick="window.location.href='add_announcement.php'">
                <i class="fas fa-plus"></i> Add New Announcement
            </button>
        </div>
    </div>

    <div class="table">
        <table>
            <thead>
                <tr>
                    <th><span class="circle-icon" title="Select all"><i class="far fa-circle"></i></span></th>
                    <th>ID</th>
                    <th>Department</th>
                    <th>Content</th>
                    <th>Display Date</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($announcements)): ?>
                    <?php foreach ($announcements as $a): ?>
                        <tr>
                            <td><span class="circle-icon" title="Select row"><i class="far fa-circle"></i></span></td>
                            <td><?= htmlspecialchars($a['id']) ?></td>
                            <td><?= htmlspecialchars($a['department']) ?></td>
                            <td><?= htmlspecialchars($a['content']) ?></td>
                            <td><?= htmlspecialchars($a['display_date']) ?></td>
                            <td><?= htmlspecialchars($a['created_at']) ?></td>
                            <td>
                                <div class="action-buttons">
                                    <a href="edit_announcement.php?id=<?= $a['id'] ?>" class="action-btn"><i class="fas fa-edit"></i></a>
                                    <a href="delete_announcement.php?id=<?= $a['id'] ?>" class="action-btn delete" onclick="return confirm('Delete this announcement?')"><i class="fas fa-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="no-records">No announcements found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
