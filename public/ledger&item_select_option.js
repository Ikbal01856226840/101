

//item tree function
function get_item_recursive(path){
    $.ajax({
        url: path,
        method: 'GET',
        dataType: 'json',
        async: false,
        success: function(response){
           let tree;
            tree+=`<option value="">---Select---</option>`;
            tree+=`<optgroup label="Prmary">Prmary</optgroup>`;
            tree+=getTreeViewSelectOptionItemTree(response.data,0,0);
            $('.stock_item').html(tree);
        }
    });
}

//item tree function
function get_ledger_recursive(path){
    $.ajax({
        url: path,
        method: 'GET',
        dataType: 'json',
        async: false,
        success: function(response){
           var tree;
            tree+=`<option value="0">--All--</option>`;
            tree+=getTreeViewSelectOptionLedgerTree(response.data,0,0);
            $('.ledger_id').html(tree);
        }
    });
}

function getTreeViewSelectOptionItemTree(arr, depth = 0, chart_id = 0) {
    const a = '&nbsp;&nbsp;&nbsp;&nbsp;';
    const htmlArray = [];
    
    arr.forEach((item) => {
        const h = a.repeat(depth);

        if (item.under !== 0) {
            if (chart_id!== item.stock_group_id) {
                htmlArray.push(`<optgroup label="&nbsp;${h}${item.stock_group_name}">&nbsp;${h}${item.stock_group_name}</optgroup>`);
                chart_id = item.stock_group_id;
            }

            if (item.stock_item_id !== null) {
                htmlArray.push(`<option value="${item.stock_item_id}">&nbsp;${a}${h}${item.product_name}</option>`);
            }
        }

        if ('children' in item) {
            htmlArray.push(getTreeViewSelectOptionItemTree(item.children, depth + 1, chart_id));
        }
    });

    return htmlArray.join('');
}

function getTreeViewSelectOptionLedgerTree(arr, depth = 0, chart_id = 0) {
    const a = '&nbsp;&nbsp;&nbsp;&nbsp;';
    const htmlArray = [];
    
    arr.forEach((ledger) => {
        const h = a.repeat(depth);

        if (ledger.under !== 0) {
            if (chart_id !== ledger.group_chart_id) {
                htmlArray.push(`<optgroup label="&nbsp;${h}${ledger.group_chart_name}">&nbsp;${h}${ledger.group_chart_name}</optgroup>`);
                chart_id =ledger.group_chart_id;
            }

            if (ledger.ledger_head_id!== null) {
                htmlArray.push(`<option value="${ledger.ledger_head_id}">&nbsp;${a}${h}${ledger.ledger_name}</option>`);
            }
        }

        if ('children' in ledger) {
            htmlArray.push(getTreeViewSelectOptionLedgerTree(ledger.children, depth + 1, chart_id));
        }
    });

    return htmlArray.join('');
}