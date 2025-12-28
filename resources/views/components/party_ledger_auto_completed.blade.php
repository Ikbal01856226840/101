<input
    type="text"
    class="form-control party_ledger"
    id="party_ledger"
    value="{{$party_ledger??''}}"
    onkeyup="PartyLedgerAutoComplete.call(this)"
    onfocus="PartyLedgerAutoComplete.call(this)"
    onclick="PartyLedgerAutoComplete.call(this)"
/>
<input
    type="number"
    step="any"
    name="ledger_id"
    id="ledger_id"
    class="ledger_id"
    value="{{$ledger_id??null}}"
    style="display: none;"
    onchange="GetPartyLedgerName.call(this)"
/>

<script>
    function PartyLedgerAutoComplete() {
        const input = $(this);
        const homeRoute = $("#homeRoute").attr("href");

        // Prevent re-binding autocomplete multiple times
        if (input.data('autocomplete-initialized')) return;

        input.autocomplete({
            delay: 500,
            source: function(request, response) {
                $.ajax({
                    type: 'GET',
                    dataType: 'json',
                    url: "{{ route('report-ledger-search') }}",
                    data: {
                        name: request.term,
                        voucher_id:0
                    },
                    success: function(data) {
                        // response($.map(data, function(item) {
                        //     return {
                        //         label: item.ledger_name,
                        //         value: item.ledger_name,
                        //         ledger_head_id: item.ledger_head_id,
                        //         inventory_value: item.inventory_value
                        //     };
                        // }));
                        // Map DB results
                        const results = $.map(data, function(item) {
                            return {
                                label: item.ledger_name,
                                value: item.ledger_name,
                                ledger_head_id: item.ledger_head_id,
                                inventory_value: item.inventory_value
                            };
                        });

                        // Add the static "All" option at the top
                         const term = (request.term || '').trim().toLowerCase();
                        if (['a', 'al', 'all'].some(prefix => term.startsWith(prefix))) {
                            results.unshift({
                                label: "All",
                                value: "All",
                                ledger_head_id: 0,
                                inventory_value: 0
                            });
                        }

                        response(results);
                    }
                });
            },
            change: function(event, ui) {
                if (!ui.item) {
                    input.val('');
                    input.focus();
                    $("#ledger_id").val("").trigger('change')
                }
            },
            select: function (event, ui) {
                input.val(ui.item.value);
                 $('.ledger_id').val(ui.item.ledger_head_id).trigger('change');
            },
            minLength: 1
        }).on('keydown', function(event) {
            // If TAB key is pressed and menu is open, simulate selection of first item
            if (event.key === "Tab") {
                const menu = input.autocomplete("widget");
                if (menu.is(":visible")) {
                    event.preventDefault();
                    menu.find("li:first-child").trigger("click");
                }
            }
        });
        input.data('autocomplete-initialized', true);
    }

    function GetPartyLedgerName() {
        console.log($(this).val());
        const id = $(this).val();
        const partyLedgerInput = $('#party_ledger');
        const homeRoute = $("#homeRoute").attr("href");

        if ((!(partyLedgerInput?.val()||'')?.trim() || partyLedgerInput?.val().toLowerCase()=='all') && id) {
            $.ajax({
                type: 'GET',
                dataType: 'json',
                url: `${homeRoute}/searching-party-ledger-name`,
                data: { id: id },
                success: function(data) {
                    if (data?.ledger_name) {
                        partyLedgerInput.val(data?.ledger_name);
                    }
                }
            });
        }
    }


</script>
