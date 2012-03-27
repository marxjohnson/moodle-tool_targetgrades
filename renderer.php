<?php
### @export "datalist"
class tool_targetgrades_renderer extends plugin_renderer_base {
    public function datalist($options, $name, $default = '') {
        $listid = uniqid('datalist');
        $select = html_writer::select($options, $name, $default);
        $input = html_writer::empty_tag('input', array('name' => $name, 'list' => $listid, 'value' => $default));
        $datalist = html_writer::tag('datalist', $select, array('id' => $listid));
        return $datalist.$input;
    }
}
### @end

?>
