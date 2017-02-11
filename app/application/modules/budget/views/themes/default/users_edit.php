<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h2>
                Users
                <a  href="<?= base_url('admin/users') ?>" class="btn btn-warning">Go back to users listing</a>
            </h2>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Update user
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <form role="form" method="POST" action="<?=base_url('admin/users/edit/'.$user->id)?>">
                                <div class="form-group">
                                    <label>User Id</label>
                                    <input class="form-control" value="<?=$user->id?>" placeholder="Auto generated" disabled="1">
                                </div>
                                <div class="form-group">
                                    <label>Username</label>
                                    <input class="form-control" value="<?=$user->username?>" placeholder="Auto generated" disabled="1">
                                </div>
                                <div class="form-group">
                                    <label>Email</label>
                                    <input class="form-control" value="<?=$user->email?>" placeholder="Enter group description" id="email" name="email">
                                </div>
                                <div class="form-group">
                                    <label>User Group</label>
                                    <select class="form-control" id="group_id" name="group_id">
                                        <?php foreach ($groups as $group): ?>
                                        <option value="<?=$group->id?>" <?=ui_selected_item($user_group->name,$group->name)?></optio><?=$group->name?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>First Name</label>
                                    <input class="form-control" value="<?=$user->first_name?>" placeholder="Enter group description" id="first_name" name="first_name">
                                </div>
                                <div class="form-group">
                                    <label>Last Name</label>
                                    <input class="form-control" value="<?=$user->last_name?>" placeholder="Enter group description" id="last_name" name="last_name">
                                </div>
                                <div class="form-group">
                                    <label>Phone</label>
                                    <input class="form-control" value="<?=$user->phone?>" placeholder="Enter group description" id="phone" name="phone">
                                </div>
                                <button type="submit" class="btn btn-primary">Submit Button</button>
                            </form>
                        </div>


                    </div>
                    <!-- /.row (nested) -->
                </div>
                <!-- /.panel-body -->
            </div>
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
</div>
<!-- /#page-wrapper -->
