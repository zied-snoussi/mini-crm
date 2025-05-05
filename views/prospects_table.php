<table class="prospects-table">
    <thead>
        <tr>
            <th>Name</th>
            <th>Company</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($result->rowCount() > 0): ?>
            <?php while ($row = $result->fetch()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['company']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td><?php echo htmlspecialchars($row['phone']); ?></td>
                    <td>
                        <span class="status-badge status-<?php echo htmlspecialchars($row['status']); ?>">
                            <?php echo ucfirst(htmlspecialchars($row['status'])); ?>
                        </span>
                    </td>
                    <td class="actions">
                        <!-- View button -->
                        <a href="prospect_detail.php?id=<?php echo htmlspecialchars($row['id']); ?>" 
                           class="btn btn-sm btn-secondary" 
                           aria-label="View details for <?php echo htmlspecialchars($row['name']); ?>">View</a>
                        
                        <!-- Edit button -->
                        <a href="prospect_form.php?id=<?php echo htmlspecialchars($row['id']); ?>" 
                           class="btn btn-sm btn-secondary" 
                           aria-label="Edit details for <?php echo htmlspecialchars($row['name']); ?>">Edit</a>
                        
                        <!-- Delete button -->
                        <button class="btn btn-sm btn-danger delete-prospect" 
                                data-id="<?php echo htmlspecialchars($row['id']); ?>" 
                                onclick="confirmDelete(this)" 
                                aria-label="Delete <?php echo htmlspecialchars($row['name']); ?>">Delete</button>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="6" class="no-data">No prospects found. Start by adding one!</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<script>
    /**
     * Confirm before deleting a prospect.
     * @param {HTMLElement} button - The delete button element.
     */
    function confirmDelete(button) {
        const prospectId = button.getAttribute('data-id');
        if (confirm('Are you sure you want to delete this prospect?')) {
            // Trigger the delete action (this should be handled via AJAX or form submission)
            deleteProspect(prospectId);
        }
    }

    /**
     * Handle prospect deletion (example implementation).
     * Replace this with your actual AJAX or form submission logic.
     * @param {string} prospectId - The ID of the prospect to delete.
     */
    function deleteProspect(prospectId) {
        console.log(`Prospect with ID ${prospectId} will be deleted.`);
        // Add your AJAX or form submission logic here.
    }
</script>