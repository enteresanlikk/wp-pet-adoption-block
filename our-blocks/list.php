<?php
    require_once PET_ADOPTION_PATH . 'inc/PetService.php';
    $petService = new PetService();
?>

<p>This page took <strong><?php echo timer_stop();?></strong> seconds to prepare. Found <strong><?= number_format($petService->count) ?></strong> results (showing the first <?= count($petService->pets) ?>).</p>

<table class="pet-adoption-table">
    <tr>
        <th>Name</th>
        <th>Species</th>
        <th>Weight</th>
        <th>Birth Year</th>
        <th>Hobby</th>
        <th>Favorite Color</th>
        <th>Favorite Food</th>
        <?php if(current_user_can('administrator')): ?>
            <th>Actions</th>
        <?php endif; ?>
    </tr>
    <?php foreach($petService->pets as $pet): ?>
        <tr>
            <td>
                <?= $pet->petname ?>
            </td>
            <td>
                <?= $pet->species ?>
            </td>
            <td>
                <?= $pet->petweight ?>
            </td>
            <td>
                <?= $pet->birthyear ?>
            </td>
            <td>
                <?= $pet->favhobby ?>
            </td>
            <td>
                <?= $pet->favcolor ?>
            </td>
            <td>
                <?= $pet->favfood ?>
            </td>
            <?php if(current_user_can('administrator')): ?>
                <td>
                    <form action="<?= esc_url(admin_url('admin-post.php')) ?>" method="post">
                        <input type="hidden" name="action" value="delete_pet">
                        <input type="hidden" name="id" value="<?= $pet->id ?>">
                        <button class="delete-pet-button">
                            X
                        </button>
                    </form>
                </td>
            <?php endif; ?>
        </tr>
    <?php endforeach; ?>
</table>

<?php if(current_user_can('administrator')): ?>
    <form action="<?= esc_url(admin_url('admin-post.php')) ?>" method="post" class="create-pet-form">
        <p>
            Enter only pet name.
        </p>
        <input type="hidden" name="action" value="create_pet">

        <input type="text" name="petname" placeholder="Pet Name" required>
        <input type="submit" value="Create Pet">
    </form>
<?php endif; ?>