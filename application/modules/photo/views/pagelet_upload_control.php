<span class="upload-control">
    <?php foreach (array('progress', 'holder', 'item', 'error') as $template_name): ?>
        <script type="text/template" class="js-<?php echo $template_name; ?>-template">
            <?php echo ${$template_name . '_template'}; ?>
        </script>
    <?php endforeach; ?>

    <?php if ( ! empty($message)): ?>
        <p><?php echo $message; ?></p>
    <?php endif; ?>

    <input id="<?php echo $id; ?>"
        type="file" name="files[]" <?php ($is_multiple) && print('multiple'); ?>

        data-parent="<?php echo $parent; ?>"
        data-progress-target="<?php echo $progress_target; ?>"
        data-holder-target="<?php echo $holder_target; ?>"
        <?php if ( ! empty($image_holder_target)): ?>
            data-image-holder-target="<?php echo $image_holder_target; ?>"
        <?php endif; ?>
    >
    <?php if ($profile_pic) : ?>
        <p id="file_container"><input type="hidden" id="file_hidden" name="file_name" value="<?php $profile_pic; ?>" /><span id="file_name"><?php echo $profile_pic; ?></span><a href="#" data-id="<?php echo $user_id; ?>" title="remove" id="file_remove"><span class="glyphicon glyphicon-remove text-danger"></span></a></p>
    <?php else: ?>
        <p id="file_container" style="display:none;"><input type="hidden" id="file_hidden" name="file_name" value="nophoto.png" /><span id="file_name">nophoto.png</span><a href="#" data-id="<?php echo $user_id; ?>" title="remove" id="file_remove"><span class="glyphicon glyphicon-remove text-danger"></span></a></p>
    <?php endif; ?>
        
</span>
<div id="files"></div>