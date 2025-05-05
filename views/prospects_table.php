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
        <?php if($result->rowCount() > 0): ?>
            <?php while($row = $result->fetch()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['company']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td><?php echo htmlspecialchars($row['phone']); ?></td>
                    <td><span class="status-badge status-<?php echo $row['status']; ?>"><?php echo ucfirst($row['status']); ?></span></td>
                    <td class="actions">
                        <a href="prospect_detail.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-secondary">View</a>
                        <a href="prospect_form.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-secondary">Edit</a>
                        <button class="btn btn-sm btn-danger delete-prospect" data-id="<?php echo $row['id']; ?>">Delete</button>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="6" class="no-data">No prospects found</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
