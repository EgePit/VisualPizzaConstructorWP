<div id="customize">
    <?php foreach($this->settings_fields as $group_title=> $group_fields): ?>
    <div class="input-group">
        <legend><?php echo $group_title; ?></legend>
        <?php foreach($group_fields as $group=> $fields) : ?>
            <?php foreach($fields as $field_name=> $field) : ?>
                <div>
                    <label>
                        <?php echo ucfirst($field_name) ?>:
                        <input name="<?php echo $group ?>[<?php echo $field_name ?>][val]" type="<?php echo $field['type'] ?>" value="<?php echo $saved_styles[$group][$field_name]['val'] ?>" size="4" />
                        <?php if($field['unit']) : ?>
                        <select name="<?php echo $group ?>[<?php echo $field_name ?>][unit]">
                        <?php foreach($field['unit'] as $unit_label=> $unit) : ?>
                            <option <?php echo $saved_styles[$group][$field_name]['unit'] == $unit ? 'selected' : '' ?> value="<?php echo $unit ?>"><?php echo $unit_label ?></option>
                        <?php endforeach; ?>
                        </select>
                        <?php endif; ?>
                    </label>
                </div>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </div>
    <?php endforeach; ?>
</div>