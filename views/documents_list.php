<?php if(count($documents) > 0): ?>
    <div class="documents-list">
        <?php foreach($documents as $doc): ?>
            <div class="document">
                <div class="document-info">
                    <span class="document-name"><?php echo htmlspecialchars($doc['original_filename']); ?></span>
                    <span class="document-date"><?php echo date('M j, Y', strtotime($doc['created_at'])); ?></span>
                </div>
                <div class="document-actions">
                    <a href="uploads/<?php echo $doc['filename']; ?>" target="_blank" class="btn btn-sm btn-secondary">View</a>
                    <button class="btn btn-sm btn-danger delete-document" data-id="<?php echo $doc['id']; ?>">Delete</button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <p class="no-data">No documents yet.</p>
<?php endif; ?>
