<?php if(count($notes) > 0): ?>
    <?php foreach($notes as $note): ?>
        <div class="note">
            <div class="note-header">
                <span class="note-author"><?php echo htmlspecialchars($note['username']); ?></span>
                <span class="note-date"><?php echo date('M j, Y g:i A', strtotime($note['created_at'])); ?></span>
            </div>
            <div class="note-content"><?php echo nl2br(htmlspecialchars($note['content'])); ?></div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p class="no-data">No notes yet.</p>
<?php endif; ?>
