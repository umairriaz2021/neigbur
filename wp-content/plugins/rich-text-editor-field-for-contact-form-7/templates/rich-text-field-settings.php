<?php
    global $cf7rt_rich_text_params;
    extract($cf7rt_rich_text_params);
?>


<div class="control-box">
    <fieldset>
    <legend><?php _e('Generate a form-tag for a Rich Text Editor using WordPress wp_editor. For more details, see <a href="http://www.wpexpertdeveloper.com/contact-form7-rich-text-editor/">Rich Text Field</a>.','cf7rt');?></legend>

    <table class="form-table">
    <tbody>
        <tr>
            <th scope="row"><?php _e('Field type','cf7rt'); ?></th>
            <td>
                <fieldset>
                    <legend class="screen-reader-text"><?php _e('Field type','cf7rt'); ?></legend>
                    <label><input type="checkbox" name="required"><?php _e('Required field','cf7rt'); ?></label>
                </fieldset>
            </td>
        </tr>

        <tr>
            <th scope="row"><label for="tag-generator-panel-rich-text-name"><?php _e('Name','cf7rt'); ?></label></th>
            <td><input type="text" name="name" class="tg-name oneline" id="tag-generator-panel-rich-text-name"></td>
        </tr>

        <tr>
            <th scope="row"><label for="tag-generator-panel-rich-text-values"><?php _e('Default Value','cf7rt'); ?></label></th>
            <td><input type="text" name="values" class="oneline" id="tag-generator-panel-rich-text-values"><br>
            <label><input type="checkbox" name="placeholder" class="option"><?php _e('Use this text as the placeholder of the field.','cf7rt'); ?></label></td>
        </tr>

        <tr>
            <th scope="row"><label for="tag-generator-panel-rich-text-id"><?php _e('ID','cf7rt'); ?></label></th>
            <td><input type="text" name="id" class="idvalue oneline option" id="tag-generator-panel-rich-text-id"></td>
        </tr>

        <tr>
            <th scope="row"><label for="tag-generator-panel-rich-text-class"><?php _e('Class','cf7rt'); ?></label></th>
            <td><input type="text" name="class" class="classvalue oneline option" id="tag-generator-panel-rich-text-class"></td>
        </tr>

        <tr>
            <th scope="row"><label for="tag-generator-panel-rich-text-rows"><?php _e('Rows','cf7rt'); ?></label></th>
            <td><input type="text" name="rows" class="rowsvalue oneline option" id="tag-generator-panel-rich-text-rows"></td>
        </tr>

        

    </tbody>
    </table>
    </fieldset>

    <div class="insert-box">
        <input type="text" name="<?php echo $type; ?>" class="tag code" readonly="readonly" onfocus="this.select()" />

        <div class="submitbox">
        <input type="button" class="button button-primary insert-tag" value="<?php echo esc_attr( __( 'Insert Tag', 'cf7rt' ) ); ?>" />
        </div>

        <br class="clear" />

    </div>
</div>