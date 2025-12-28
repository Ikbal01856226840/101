<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class Tree
{
    public function buildTree(array $elements, $parentId, $t, $id, $under, $product_id = null)
    {
        //    dd($elements);
        $branch = [];

        foreach ($elements as $element) {

            if ($element[$under] == $parentId) {

                if ($element[$id] != $t) {

                    $children = $this->buildTree($elements, $element[$id], $t, $id, $under);
                    if ($children) {
                        if ($element[$id] != $t) {
                            $element['children'] = $children;
                        }
                    }
                    $t = $element[$id];
                }

                $branch[] = $element;
            } elseif (! empty($element[$id]) && $element[$id] == $parentId) {
                if (! empty($element[$product_id])) {
                    $branch[] = $element;
                }
            }
        }

        return array_reverse($branch);
    }

    public function getTreeViewSelectOption($arr, $depth, $id, $under, $name, $under_id = null)
    {
        $html = '';
        foreach ($arr as $v) {
            if ($v[$under] != 0) {
                if (! empty($v[$id])) {
                    if (! empty($under_id)) {

                        $html .= '<option value="'.$v[$id].'-'.$v[$under].'">';
                    } else {
                        $html .= '<option value="'.$v[$id].'">';
                    }
                    $html .= str_repeat('&nbsp;&nbsp;&nbsp; ', $depth);
                    $html .= '&nbsp;&nbsp;&nbsp;'.$v[$name].'</option>'.PHP_EOL;
                }
            }
            if (array_key_exists('children', $v)) {

                $html .= $this->getTreeViewSelectOption($v['children'], $depth + 1, $id, $under, $name, $under_id);
            }
        }

        return $html;
    }

    public function group_chart_tree_row_query($group)
    {
        $data = DB::select("with recursive tree as
        (SELECT group_chart.group_chart_id,group_chart.group_chart_name,group_chart.under,1 AS lvl FROM group_chart WHERE FIND_IN_SET(group_chart.group_chart_id,'$group')
         UNION
         SELECT E.group_chart_id,E.group_chart_name,E.under,H.lvl+1 as lvl FROM tree H JOIN group_chart E ON H.group_chart_id=E.under
         )
         SELECT * FROM tree");

        return json_decode(json_encode($data, true), true);
    }

    public function getTreeViewSelectOptionLedgerTree($data, $parentId, $level = 0, $t = 0)
    {
        $options = '';
        foreach ($data as $item) {

            if ($item['under'] == $parentId) {
                if ($item['group_chart_id'] != $t) {
                    $prefix = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', $level); // add a prefix to indicate level
                    $ledger_space = '&nbsp;&nbsp;&nbsp;';
                    $options .= '<optgroup label="'.$prefix.$item['group_chart_name'].'">'.$prefix.$item['group_chart_name'].'</optgroup>';
                    $ledger_data = DB::table('ledger_head')->where('group_id', $item['group_chart_id'])->get();

                    foreach ($ledger_data as $ledger) {
                        $options .= '<option value="'.$ledger->ledger_head_id.'">'.$ledger_space.$prefix.$ledger->ledger_name.'</option>';
                    }
                    $options .= $this->getTreeViewSelectOptionLedgerTree($data, $item['group_chart_id'], $level + 1, $t); // recursively add child options
                    $t = $item['group_chart_id'];
                }
            }
        }

        return $options;
    }

    public function getTreeViewSelectOptionGroup_chart_two($data, $parentId, $level = 0, $t = 0)
    {
        $options = '';
        foreach ($data as $item) {

            if ($item['under'] == $parentId) {
                if ($item['group_chart_id'] != $t) {
                    $prefix = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', $level); // add a prefix to indicate level
                    $options .= '<option value="'.$item['group_chart_id'].'">'.$prefix.$item['group_chart_name'].'</option>';
                    $options .= $this->getTreeViewSelectOptionGroup_chart_two($data, $item['group_chart_id'], $level + 1, $t); // recursively add child options
                    $t = $item['group_chart_id'];
                }
            }
        }

        return $options;
    }

    public function group_chart_tree_row_query_without_param()
    {
        $data = DB::select("with recursive tree as
          (SELECT group_chart.group_chart_id,group_chart.group_chart_name,group_chart.under,1 AS lvl FROM group_chart Where group_chart.group_chart_name != 'Reserved'
           UNION
           SELECT E.group_chart_id,E.group_chart_name,E.under,H.lvl+1 as lvl FROM tree H JOIN group_chart E ON H.group_chart_id=E.under
           )
           SELECT * FROM tree");

        return json_decode(json_encode($data, true), true);
    }
}
