self.onmessage = function (e) {
    const { arr, children_sum, checkboxStates } = e.data;

    // Precompute children_sum map for fast lookup
    const childrenMap = new Map(children_sum.map(c => [c.stock_group_id, c]));

    let htmlFragments = [];
    let i = 1; // Item index counter for each row
    let total_opening_value = 0;
    let total_opening_qty =0;
    let total_inwards_qty = 0;
    let total_inwards_value = 0;
    let total_outwards_qty = 0;
    let total_outwards_value = 0;
    let total_clasing_qty = 0;
    let total_clasing_value = 0;
    function getTreeView(arr, children_sum, depth = 0, chart_id = 0) {
        arr.forEach(v => {
            const h = '&nbsp;'.repeat(depth);

            if (chart_id !== v.stock_group_id) {
                const matchingChild = childrenMap.get(v.stock_group_id) || {};
                const totalOpQty = matchingChild.total_op_qty || 0;
                const stockQtyIn = matchingChild.stock_qty_in || 0;
                const stockQtyOut = matchingChild.stock_qty_out || 0;

                if (totalOpQty || stockQtyIn || stockQtyOut) {
                    const openingRate =(matchingChild.sum_op_value||0)/Math.abs(totalOpQty||1);
                    const closingQty = (totalOpQty + stockQtyIn) - stockQtyOut;
                    const closingRate = (matchingChild.sum_current_value || 0) / Math.abs(closingQty || 1);

                    htmlFragments.push(`
                        <tr class="left left-data editIcon" id="${v.stock_group_id}-${v.under}">
                            <td style="width: 1%; border: 1px solid #ddd;"></td>
                            <td class="table-row_tree" style="width: 3%; border: 1px solid #ddd; color: #0B55C4; font-weight: bold;">
                                <p style="margin-left: ${(h + '&nbsp;&nbsp;').length - 12}px;" class="text-wrap mb-0 pb-0">${v.stock_group_name}</p>
                            </td>
                            ${checkboxStates.op_qty_check ? `<td class="td text-end" style="width: 3%; font-weight: bold;">${totalOpQty.formatBangladeshCurrencyType('quantity')}</td>` : ''}
                            ${checkboxStates.op_rate_check ? `<td class="td text-end" style="width: 3%; font-weight: bold;">${openingRate.formatBangladeshCurrencyType('rate')}</td>` : ''}
                            ${checkboxStates.op_value_check ? `<td class="td text-end" style="width: 5%; font-weight: bold;">${(matchingChild.sum_op_value || 0).formatBangladeshCurrencyType('amount')}</td>` : ''}
                            ${checkboxStates.in_qty_check ? `<td class="td text-end" style="width: 3%; font-weight: bold;">${stockQtyIn.formatBangladeshCurrencyType('quantity')}</td>` : ''}
                            ${checkboxStates.in_rate_check ? `<td class="td text-end" style="width: 3%; font-weight: bold;">${((matchingChild.stock_total_sum_in || 0) / (stockQtyIn || 1)).formatBangladeshCurrencyType('rate')}</td>` : ''}
                            ${checkboxStates.in_value_check ? `<td class="td text-end" style="width: 5%; font-weight: bold;">${(matchingChild.stock_total_sum_in || 0).formatBangladeshCurrencyType('amount')}</td>` : ''}
                            ${checkboxStates.out_qty_check ? `<td class="td text-end" style="width: 3%; font-weight: bold;">${stockQtyOut.formatBangladeshCurrencyType('quantity')}</td>` : ''}
                            ${checkboxStates.out_rate_check ? `<td class="td text-end" style="width: 3%; font-weight: bold;">${((matchingChild.stock_total_sum_out || 0) / (stockQtyOut || 1)).formatBangladeshCurrencyType('rate')}</td>` : ''}
                            ${checkboxStates.out_value_check ? `<td class="td text-end" style="width: 5%; font-weight: bold;">${(matchingChild.stock_total_sum_out || 0).formatBangladeshCurrencyType('amount')}</td>` : ''}
                            ${checkboxStates.closing_qty_check ? `<td class="td text-end" style="width: 3%; font-weight: bold;">${closingQty.formatBangladeshCurrencyType('quantity')}</td>` : ''}
                            ${checkboxStates.closing_rate_check ? `<td class="td text-end" style="width: 3%; font-weight: bold;">${closingRate.formatBangladeshCurrencyType('rate')}</td>` : ''}
                            ${checkboxStates.closing_value_check ? `<td class="td text-end" style="width: 5%; font-weight: bold;">${(matchingChild.sum_current_value || 0).formatBangladeshCurrencyType('amount')}</td>` : ''}
                        </tr>
                    `);
                }

                chart_id = v.stock_group_id;
            }

            if (v.op_qty || v.stock_in_sum_qty || v.stock_out_sum_qty) {
                           const closingQty = (v.op_qty + v.stock_in_sum_qty) - v.stock_out_sum_qty;
                            total_opening_qty += v.op_qty || 0;
                            total_opening_value += (v.op_qty * v.op_rate);
                            total_inwards_qty += (v.stock_in_sum_qty || 0);
                            total_inwards_value += (v.stock_total_in || 0);
                            total_outwards_qty += (v.stock_out_sum_qty || 0);
                            total_outwards_value += (v.stock_total_out || 0);
                            total_clasing_qty += (closingQty || 0);
                            total_clasing_value += ((closingQty || 0) * (v.current_rate || 0));
                            if (checkboxStates.stock_with_out_item_group_check) {
                                    if (!checkboxStates.show_closing_is || closingQty !== 0) {
                                        stock_item_data_show(
                                            htmlFragments,
                                            v,
                                            closingQty,
                                            h,
                                            '&nbsp;',
                                            ...Object.values(checkboxStates)
                                        );
                                    }
                                }
            }

            if ('children' in v) {
                getTreeView(v.children, children_sum, depth + 1, chart_id);
            }
        });
    }

    function stock_item_data_show(htmlFragments, v, closing_qty, h, a, op_qty_check, op_rate_check, op_value_check, in_qty_check, in_rate_check, in_value_check, out_qty_check, out_rate_check, out_value_check, closing_qty_check, closing_rate_check, closing_value_check) {
        const marginLeft = `${(h + a.repeat(3)).length - 12}px`;
        const opValue = (v.op_qty || 0) * (v.op_rate || 0);
        const inRate = (v.stock_total_in || 0) / (v.stock_in_sum_qty || 1);
        const outRate = (v.stock_total_out || 0) / (v.stock_out_sum_qty || 1);
        const closingValue = (closing_qty || 0) * (v.current_rate || 0);

        htmlFragments.push(`
            <tr id="${v.stock_item_id}" class="lleft left-data table-row">
                <td class="sl" style="width: 1%; border: 1px solid #ddd;">${i++}</td>
                <td style="width: 5%; border: 1px solid #ddd; color: #0B55C4;" class="item_name">
                    <p style="margin-left:${marginLeft}" class="text-wrap mb-0 pb-0">${v.product_name}</p>
                </td>
                ${op_qty_check ? `<td class="td text-end" style="width: 3%;">${(v.op_qty || 0).formatBangladeshCurrencyType("quantity", v.symbol)}</td>` : `<td class="d-none"></td>`}
                ${op_rate_check ? `<td class="td text-end" style="width: 3%;">${(v.op_rate || 0).formatBangladeshCurrencyType("rate")}</td>` : `<td class="d-none"></td>`}
                ${op_value_check ? `<td class="td text-end" style="width: 5%;">${opValue.formatBangladeshCurrencyType("amount")}</td>` : `<td class="d-none"></td>`}
                ${in_qty_check ? `<td class="td text-end" style="width: 3%;">${(v.stock_in_sum_qty || 0).formatBangladeshCurrencyType("quantity", v.symbol)}</td>` : `<td class="d-none"></td>`}
                ${in_rate_check ? `<td class="td text-end" style="width: 3%;">${inRate.formatBangladeshCurrencyType("rate")}</td>` : `<td class="d-none"></td>`}
                ${in_value_check ? `<td class="td text-end" style="width: 5%;">${(v.stock_total_in || 0).formatBangladeshCurrencyType("amount")}</td>` : `<td class="d-none"></td>`}
                ${out_qty_check ? `<td class="td text-end" style="width: 3%;">${(v.stock_out_sum_qty || 0).formatBangladeshCurrencyType("quantity", v.symbol)}</td>` : `<td class="d-none"></td>`}
                ${out_rate_check ? `<td class="td text-end" style="width: 3%;">${outRate.formatBangladeshCurrencyType("rate")}</td>` : `<td class="d-none"></td>`}
                ${out_value_check ? `<td class="td text-end" style="width: 5%;">${(v.stock_total_out || 0).formatBangladeshCurrencyType("amount")}</td>` : `<td class="d-none"></td>`}
                ${closing_qty_check ? `<td class="td text-end" style="width: 3%;">${(closing_qty || 0).formatBangladeshCurrencyType("quantity", v.symbol)}</td>` : `<td class="d-none"></td>`}
                ${closing_rate_check ? `<td class="td text-end" style="width: 3%;">${(v.current_rate || 0).formatBangladeshCurrencyType("rate")}</td>` : `<td class="d-none"></td>`}
                ${closing_value_check ? `<td class="td text-end" style="width: 5%;">${closingValue.formatBangladeshCurrencyType("amount")}</td>` : `<td class="d-none"></td>`}
            </tr>
        `);
    }

    Number.prototype.formatBangladeshCurrencyType= function(type=null,symbol='') {
        if(type=="quantity"){
            let qty_dcecimal=checkboxStates.quantity_decimals||0;
            let qty_comma=checkboxStates.show_quantity_comma_is;
            if(checkboxStates.show_units_of_measure_is)return MakeCurrency(this.toFixed(qty_dcecimal),qty_comma) + symbol;
            else return  MakeCurrency(this.toFixed(qty_dcecimal),qty_comma)
        }else if(type=="rate"){
            let rate=checkboxStates.rate_decimals||0;
            return  MakeCurrency(this.toFixed(rate))
        }else if(type=="amount"){
            let amount=checkboxStates.amount_decimals||0;
            return  MakeCurrency(this.toFixed(amount))
        }
        else{
            return  MakeCurrency(this.toFixed(0))
        }
    };
    function MakeCurrency(num,qty_comma=true){
        if(!qty_comma){
            return num;
        }else{
            let x=num?.split('.');
            x[0] = x[0].replace(/(\d)(?=(\d\d)+\d$)/g, "$1,");
            return x.join('.');
        }
        }
    getTreeView(arr, children_sum);
    self.postMessage({
        html: htmlFragments.join(''),
        totals: {
            total_opening_qty,
            total_opening_value,
            total_inwards_qty,
            total_inwards_value,
            total_outwards_qty,
            total_outwards_value,
            total_clasing_qty,
            total_clasing_value
        }
    });

};



