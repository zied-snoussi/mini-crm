<?php if (count($notes) > 0): ?>
    <?php foreach ($notes as $note): ?>
        <div class="note">
            <div class="note-header">
                <!-- Display the author and creation date -->
                <span class="note-author"><?php echo htmlspecialchars($note['username']); ?></span>
                <span class="note-date"><?php echo date('M j, Y g:i A', strtotime($note['created_at'])); ?></span>
            </div>
            <div class="note-content">
                <!-- Display the note content with line breaks preserved -->
                <?php echo nl2br(htmlspecialchars($note['content'])); ?>
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <!-- No notes message -->
    <p class="no-data">No notes have been added yet. Start by creating one!</p>
<?php endif; ?>