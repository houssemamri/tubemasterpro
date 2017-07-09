
<h1 id="target_header">
    My Campaigns List
</h1>

<div class="col-sm-12">
    <div class="col-sm-4" style="padding-left:0;">
        <h2 class="pull-left" style="margin-top:6px;margin-bottom:6px;">&nbsp;</h2>
    </div>
    <div class="form-group col-sm-3 text-right" style="margin:7px 0; padding-right:2px;">
        Add New Target List
    </div>

    <form id="search_form" autocomplete="off">
        <div class="form-group col-sm-3" style="padding-left:2px;">
            <input id="target_list_name" name="target_list_name" class="form-control" type="text" placeholder="" value="">
        </div>
        <div class="form-group col-sm-2">
            <button id="add_target_button" class="btn btn-disabled col-sm-12" disabled="disabled" type="button" data-action="<?php echo site_url('dashboard/dashboard_ajax/add_target'); ?>">Add</button>
        </div>
    </form>
</div>

<?php if ($campaigns) : ?>
    <div class="col-sm-12" style="margin-bottom:10px; padding-left:30px;">
        <input id="check_all_targets" type="checkbox" /><span style="padding:0 10px;">Check All</span>
        <button style="display:none;" id="delete_target_list_button" class="btn btn-danger" type="button" data-action="<?php echo site_url('dashboard/dashboard_ajax/delete_target_lists'); ?>">Delete Selected Target List</button>
    </div>

    <div id="target_lists" class="col-sm-12" data-action="<?php echo site_url('dashboard/dashboard_ajax/get_target_videos'); ?>" style="padding-bottom:20px;">
        <?php print_r($campaigns);?>
    </div>

<?php else: ?>
    <div class="col-sm-12">
        <div class="alert alert-lg alert-danger text-center">No Campaigns</div>
    </div>
<?php endif; ?>

<div class="modal fade text-left" id="target_list_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">

        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Confirmation</h4>
            </div>
            <div class="modal-body">

            </div>
            <div class="modal-footer">
                <button id="modal-yes" type="button" class="btn btn-danger">Yes</button>
                <button id="modal-no" type="button" class="btn btn-default" data-dismiss="modal">No</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade text-left" id="add_video_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">

        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Add Video To Target List</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal">
                    <div class="form-group">
                        <div class="col-sm-12">
                            <input id="add_video_input" name="add_video_input" class="form-control" type="text" placeholder="Paste entire YouTube URL here. e.g. https://www.youtube.com/watch?v=YoUtuB3iDh3rE" value="">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button id="add_video_btn" data-target-id="" class="btn btn-disabled" disabled="disabled" type="button" data-action="<?php echo site_url('dashboard/dashboard_ajax/add_video'); ?>">Add</button>
                <button id="modal-no" type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="mover_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">

        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Move selected videos</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal">
                    <div class="form-group">
                        <div class="col-sm-6">
                            <select id="mover_targets" name="search_filter[max]" class="form-control">
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <button id="mover_videos" data-action="<?php echo site_url('dashboard/dashboard_ajax/move_target_list_videos'); ?>" type="button" class="btn btn-success col-sm-12">Move To Target List</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="videoModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header text-right" style="padding:5px 15px;">
                <button type="button" style="float:none;font-size:30px;" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <!-- <h4 class="modal-title">BC Winegrowers Series</h4> -->
            </div>
            <div class="modal-body text-center">
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="linksModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header text-center" style="padding:5px 15px;">
                <h4 class="modal-title" id="myModalLabel">Click/Tap on the textarea below to select all the links.</h4>
            </div>
            <div class="modal-body text-center">
                <textarea class="form-control" rows="5" readonly="readonly"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div> 