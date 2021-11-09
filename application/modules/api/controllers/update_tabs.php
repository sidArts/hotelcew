    <div class="wrap-content container" id="container">
    <!-- start: PAGE TITLE -->
    <section id="page-title" class="padding-top-15 padding-bottom-15">
        <div class="row">
            <div class="col-sm-8">
                <h1 class="mainTitle"><?php echo $title; ?></h1>
            </div>
        </div>
    </section>
    <!-- end: PAGE TITLE -->
    <!-- start: WIZARD DEMO -->
    <div class="container-fluid container-fullw bg-white">
        <?php $this->load->view('admin/common/message'); ?>
        <div class="row">
            <div class="col-md-12">

                <div class="tabbable">
                    <!-- start: Tabs -->
                    <ul id="myTab1" class="nav nav-tabs">
                        <?php foreach ($tabs as $t => $tab) { ?>
                            <li>
                                <a href="#<?php echo $t; ?>" data-toggle="tab" >
                                    <?php echo $tab['label']; ?>
                                </a>
                            </li>
                        <?php } ?>
                    </ul>

                    <div class="tab-content">
                        <?php
                        foreach ($tabs as $t => $tab) {
                            $record = $tab['record'];
                            ?>
                            <div class="tab-pane fade" id="<?php echo $t; ?>">
                                <div class="row">
                                    <form class="horizontal-form" role="form" method="post" id="<?php echo $tab['form_id']; ?>" name="<?php echo $tab['form_id']; ?>" action="">
                                        <div class="col-sm-12">
                                            <input type="hidden" name="tab" value="<?php echo $t; ?>" />
                                            <?php $i = 1;
                                            ?>
                                            <div id="<?php echo $t; ?>">
                                                <div class="row">
                                                    <?php
                                                    foreach ($tab['groups'] as $keygroup => $group) {
                                                        $div = isset($group['fieldset_div_class']) && !empty($group['fieldset_div_class']) ? $group['fieldset_div_class'] : 12;
                                                        ?>
                                                        <div class="col-md-<?php echo $div; ?>">
                                                            <fieldset>
                                                                <legend>
                                                                    <?php echo $keygroup; ?>
                                                                </legend>
                                                                <div class="row">
                                                                    <?php
                                                                    $g = 0;

                                                                    foreach ($group['fields'] as $k => $field) {
                                                                        ?>
                                                                        <?php
                                                                        if ($g != 0 && $g % 2 == 0) {
                                                                            ?>
                                                                        </div>
                                                                        <div class="row">
                                                                        <?php } ?>
                                                                        <div class="col-sm-6">
                                                                            <div class="form-group">
                                                                                <?php if ($field['type'] == 'hidden') {
                                                                                    ?>
                                                                                    <input type="<?php echo $field['type']; ?>" name="<?php echo $k; ?>" class.="form-control <?php echo isset($field['class']) ? $field['class'] : ""; ?>" id="<?php echo $k; ?>" placeholder="<?php echo $field['label']; ?>" value="<?php echo isset($record[$k]) ? $record[$k] : (isset($field['value']) ? $field['value'] : ""); ?>">
                                                                                <?php } else if ($field['type'] == 'file') {
                                                                                    ?>
                                                                                    <label for="<?php echo $k; ?>" <?php echo isset($field['required']) ? 'require' : ''; ?>"  style="text-align:left;display:block;" value="<?php echo isset($record[$k]) ? $record[$k] : ""; ?>">
                                                                                        <?php echo ucwords($field['label']); ?> <?php echo isset($field['lclass']) ? "<span class='" . $field['lclass'] . "'>" : ""; ?>
                                                                                    </label>
                                                                                    <span class="btn btn-success fileinput-button" style="display:<?php echo (isset($record[$k]) && $record[$k] != '') ? 'none' : 'inline'; ?>">
                                                                                        <i class="glyphicon glyphicon-plus"></i>
                                                                                        <span>Add <?php echo $field['label']; ?></span>
                                                                                        <input type="<?php echo $field['type']; ?>" name="<?php echo isset($field['array']) ? $k . '[]' : $k; ?>" <?php if (isset($field['array'])) echo 'id="' . $k . '"'; ?> onchange="browse_file(this);" class="<?php echo isset($field['class']) ? $field['class'] : ""; ?>">
                                                                                    </span>
                                                                                    <?php echo (isset($record[$k]) && $record[$k] != '') ? ('<label>' . $record[$k . '_original'] . '<a class="btn btn-xs btn-danger" href="javascript:void(0);" onclick="remove_file(this);">remove</a></label>' ) : ""; ?>
                                                                                <?php } else {
                                                                                    ?>
                                                                                    <label for="<?php echo $k; ?>" <?php echo isset($field['required']) ? 'require' : ''; ?>"  style="text-align:left;">
                                                                                        <?php echo ucwords($field['label']); ?> <?php echo isset($field['lclass']) ? "<span class='" . $field['lclass'] . "'>" : ""; ?>
                                                                                    </label>
                                                                                    <?php if ($field['type'] == 'text' || $field['type'] == 'input' || $field['type'] == 'password') {
                                                                                        ?>
                                                                                        <input type="<?php echo $field['type']; ?>" name="<?php echo isset($field['array']) ? $k . '[]' : $k; ?>" class="form-control input-md <?php echo isset($field['class']) ? $field['class'] : ""; ?>" id="<?php echo $k; ?>" placeholder="<?php echo $field['label']; ?>" value="<?php echo isset($record[$k]) ? $record[$k] : ""; ?>"  maxlength="<?php echo isset($field['maxlength']) ? $field['maxlength'] : 200; ?>" <?php if (isset($field['disabled']) && $field['disabled'] === true) echo "disabled"; ?>>
                                                                                    <?php } else if ($field['type'] == 'select') {
                                                                                        ?>
                                                                                        <select name="<?php echo isset($field['array']) ? $k . '[]' : $k; ?>" class="cs-select cs-select-md cs-skin-slide <?php echo isset($field['class']) ? $field['class'] : ""; ?>" id="<?php echo $k; ?>">
                                                                                            <option value="">- <?php echo "Select " . $field['label']; ?> -</option>
                                                                                            <?php
                                                                                            foreach ($field['options'] as $v => $t) {
                                                                                                $selected = ( isset($record[$k]) && $v == $record[$k]) ? "selected" : "";
                                                                                                ?>
                                                                                                <option <?php echo $selected ?> value="<?php echo isset($v) ? $v : $t; ?>"><?php echo $t; ?></option>
                                                                                            <?php } ?>
                                                                                        </select>
                                                                                    <?php } else if ($field['type'] == 'radio') {
                                                                                        ?>
                                                                                        <div class="clip-radio radio-primary">
                                                                                            <?php
                                                                                            foreach ($field['options'] as $v => $t) {
                                                                                                $selected = ( isset($record[$k]) && $v == $record[$k]) ? "checked" : "";
                                                                                                ?>
                                                                                                <input <?php echo $selected; ?> type="radio" id="<?php echo create_slug($t); ?>" name="<?php echo $k; ?>" value="<?php echo isset($v) ? $v : $t; ?>">
                                                                                                <label for="<?php echo create_slug($t); ?>"><?php echo $t; ?></label>
                                                                                            <?php } ?>
                                                                                        </div>
                                                                                    <?php } else if ($field['type'] == 'checkbox') {
                                                                                        ?>
                                                                                        <div class="checkbox clip-check check-primary checkbox-inline">
                                                                                            <?php
                                                                                            foreach ($field['options'] as $v => $t) {
                                                                                                $selected = ( isset($record[$k]) && $v == $record[$k]) ? "checked" : "";
                                                                                                ?>
                                                                                                <input <?php echo $selected; ?> type="checkbox" id="<?php echo create_slug($t); ?>" name="<?php echo $k; ?>[]" value="<?php echo isset($v) ? $v : $t; ?>">
                                                                                                <label for="<?php echo create_slug($t); ?>"><?php echo $t; ?></label>
                                                                                            <?php } ?>
                                                                                        </div>
                                                                                    <?php } else if ($field['type'] == 'textarea') {
                                                                                        ?>
                                                                                        <textarea name="<?php echo $k; ?>" class="form-control <?php echo isset($field['class']) ? $field['class'] : ""; ?>" id="<?php echo $k; ?>" placeholder="<?php echo $field['label']; ?>" rows="<?php echo isset($field['rows']) ? $field['rows'] : ''; ?>"><?php echo isset($record[$k]) ? $record[$k] : ""; ?></textarea>
                                                                                    <?php } ?>
                                                                                <?php } ?>
                                                                            </div>
                                                                        </div>

                                                                        <?php $g++; ?>
                                                                    <?php } ?>
                                                                </div>
                                                            </fieldset>
                                                        </div>
                                                    <?php } ?>

                                                </div>
                                            </div>
                                        </div>        
                                        <?php
                                        if (!empty($tab['groups'])) {
                                            ?>
                                            <div class="col-sm-6 pull-right">
                                                <div class="pull-right margin-top-25">
                                                    <input type="submit" class="btn btn-primary" value="Save" />
                                                    <button type="reset" id="cancel_btn" class="btn btn-md">Reset</button>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </form>
                                </div>
                            </div>
                        <?php } ?>

                    </div>

                    <!-- end: WIZARD FORM -->
                </div>
            </div>
        </div>
        <!-- start: WIZARD DEMO -->
    </div>
</div>
<script>
    var tab = "#<?php echo $active_tab; ?>";
    $(function () {
        $('body').on('focus', ".datepicker", function () {
            $(this).datepicker({
                autoclose: true,
                todayHighlight: true,
                endDate: new Date(),
                format: 'dd/mm/yyyy',
            });
        });
<?php foreach ($tabs as $t => $tab) { ?>
            var form_id = "<?php echo $tab['form_id'] ?>";
            var rules = <?php echo isset($tab['rules']) ? json_encode($tab['rules']) : ''; ?>;
            var message = <?php echo isset($tab['message']) ? json_encode($tab['message']) : ''; ?>;
            validateForm(form_id, rules, message);
<?php } ?>
        if (typeof ckeditorInit === "function")
            ckeditorInit();
    });
    $(document).ready(function () {
        $("[href=" + tab + "]").click();
    });

</script>