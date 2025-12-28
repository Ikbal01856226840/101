// pdf

const getVerticalAlign = (thd) => {
  if (thd.classList.contains('align-middle')) return 'middle';
  if (thd.classList.contains('align-top')) return 'top';
  if (thd.classList.contains('align-bottom')) return 'bottom';
  return 'middle';
};

const getMarginLeft = (thd) => {
  const pElements = thd.querySelectorAll('p');
  return pElements.length > 0
    ? parseInt(window.getComputedStyle(pElements[0]).marginLeft) / 3
    : 0.25;
};

const getHorizontalAlign = (thd) => {
  if (thd.classList.contains('text-end')) return 'right';
  if (thd.classList.contains('text-center')) return 'center';
  if (thd.classList.contains('text-left')) return 'left';

  const style = window.getComputedStyle(thd);
  return style.textAlign || 'left';
};

function getLedgerName() {
  return (
    $('.ledger_id')?.find('option:selected')?.text()?.trim() ||
    $('.party_ledger')?.val()?.trim() ||
    localStorage?.getItem('ledger_name')?.trim() ||
    ''
  );
}
function godownName() {
  return $('.godown_id')
    .find('option:selected')
    .map(function () {
      return $(this).text();
    })
    ?.get()
    ?.join(', ');
}

function getStockItemName() {
  return $('.stock_item_id').find('option:selected').text() || $('.stock_item').val();
}
function getGroupChartName() {
  return $('.group_id').find('option:selected').text();
}

function getStockGroupName() {
  return $('.stock_group_id').find('option:selected').text();
}

function dateFromTo() {
  let from_date = localStorage?.getItem('from_date')?.trim() || $('.from_date').val() || false;
  let to_date = localStorage?.getItem('to_date')?.trim() || $('.to_date').val() || false;
  let on_dated = '';
  if (from_date && to_date) {
    on_dated = from_date + ' to ' + to_date;
  } else {
    on_dated = localStorage.getItem('on_dated')?.trim();
  }
  return on_dated;
}

const preparePdfTableData = (data, element) => {
  let TableData = [];
  data.forEach((tr) => {
    let rowData = [];
    let Elements = tr.querySelectorAll(element);
    Elements.forEach((thd) => {
      let thdObj = {
        content: thd.innerText,
        styles: {
          halign: getHorizontalAlign(thd), // Horizontal alignment
          valign: getVerticalAlign(thd),
          wordBreak: thd.querySelector('p.text-wrap') ? 'break-word' : 'normal',
          cellPadding: {
            left: getMarginLeft(thd),
            right: 0.25,
            top: 0.25,
            bottom: 0.25,
          },
          fontStyle: thd.classList.contains('td-bold') ? 'bold' : 'normal',
        },
      };

      if (thd.getAttribute('rowspan')) {
        thdObj.rowSpan = parseInt(thd.getAttribute('rowspan'));
      }
      if (thd.getAttribute('colspan')) {
        thdObj.colSpan = parseInt(thd.getAttribute('colspan'));
      }
      rowData.push(thdObj);
    });
    TableData.push(rowData); // Add each row of headers as a separate entry
  });

  return TableData;
};

function generateTable(name, party_name = null) {
  let pageView = 'portrait';
  let print_header = $('#current_company_name').text();
  let print_header_address = $('#current_company_mailing_address').val();
  let print_date = localStorage.getItem('on_dated')?.trim();
  //let party_name = "fee";
  let isPdf = false;
  let mrgintop = 10;

  const { jsPDF } = window.jspdf;
  const doc = new jsPDF('p', 'mm', 'a4'); // Create PDF in portrait mode, A4 size

  if ($('#show_company_name').length) {
    //Company Name
    if ($('#show_company_name').is(':checked')) {
      let size = font_size(null, $('.company_name:checked').val()) - 6;
      doc.setFontSize(size);
      doc.text(print_header, 105, mrgintop, null, null, 'center');
    }
    //Company Mailing Address
    if ($('#show_company_mailing_address').is(':checked')) {
      let size = font_size(null, $('.company_mailing_address:checked').val()) - 6;
      mrgintop += size - 10;
      doc.setFontSize(size);
      doc.text(`Name: ${print_header_address}`, 105, mrgintop, null, null, 'center');
    }
    // Report Name
    if ($('#show_report_name').is(':checked')) {
      let size = font_size(null, $('.report_name:checked').val()) - 6;
      mrgintop += size - 10;
      doc.setFontSize(size);
      doc.text(`Name: ${name}`, 105, mrgintop, null, null, 'center');
    }

    //Party Name
    if (party_name) {
      doc.setFontSize(12);
      mrgintop += 5;
      doc.text(`Party Name: ${party_name}`, 105, mrgintop, null, null, 'center');
    }

    // Add print date and party name
    if ($('#print_date').is(':checked')) {
      mrgintop += 5;
      doc.setFontSize(12);
      doc.text(`Date: ${print_date}`, 105, mrgintop, null, null, 'center');
    }
    // Godown Name
    if ($('.godown_name_print').is(':checked')) {
      let godown_name = $('.godown_id')
        .find('option:selected')
        .map(function () {
          return $(this).text();
        })
        ?.get()
        ?.join(', ');
      if (godown_name != 'All') {
        mrgintop += 5;
        doc.setFontSize(12);
        doc.text(`Godown Name:${godown_name}`, 105, mrgintop, null, null, 'center');
      }
    }

    //Stock Item
    if ($('.stock_item_ptint').is(':checked')) {
      let item_name = $('.stock_item_id').find('option:selected').text();
      if (item_name != 'All') {
        mrgintop += 5;
        doc.setFontSize(12);
        doc.text(`Stock Item Name:${item_name?.trim()}`, 105, mrgintop, null, null, 'center');
      }
    }
    //Ledger Name
    if ($('.ledger_name_print').is(':checked')) {
      let ledger_name = $('.ledger_id').find('option:selected').text();
      if (ledger_name != '--All--') {
        mrgintop += 5;
        doc.setFontSize(12);
        doc.text(`Ledger Name:${ledger_name?.trim()}`, 105, mrgintop, null, null, 'center');
      }
    }
    mrgintop += 5;
  } else {
    // Add a title
    doc.setFontSize(16);
    doc.text(print_header, 105, mrgintop, null, null, 'center');
    mrgintop += 5;
    if (print_header_address) {
      doc.setFontSize(12);
      doc.text(`Name: ${print_header_address}`, 105, mrgintop, null, null, 'center');
      mrgintop += 5;
    }
    // main title
    if (name) {
      doc.setFontSize(12);
      doc.text(`Name: ${name}`, 105, mrgintop, null, null, 'center');
      mrgintop += 5;
    }
    if (party_name) {
      doc.setFontSize(12);
      doc.text(`Party Name: ${party_name}`, 105, mrgintop, null, null, 'center');
      mrgintop += 5;
    }

    // Add print date and party name
    if (print_date) {
      doc.setFontSize(12);
      doc.text(`Date: ${print_date}`, 105, mrgintop, null, null, 'center');
      mrgintop += 5;
    }
  }

  // Get the table data from the DOM
  let table = document.querySelector('.table_content table');
  if (!table) {
    // console.error("No table found.");
    return;
  }

  // Remove all elements with d-none class from the table
  table.querySelectorAll('.d-none').forEach((el) => el.remove());

  // Extract table headers for multiple rows in thead (handling colspan and rowspan)

  let headerRows = table.querySelectorAll('thead tr');
  let headers = preparePdfTableData(headerRows, 'th');

  // Extract table data
  let rowElements = table.querySelectorAll('tbody tr');
  let body = preparePdfTableData(rowElements, 'td');

  let footerrowElements = table.querySelectorAll('tfoot tr');
  let foot = preparePdfTableData(footerrowElements, 'th');

  // Calculate responsive font size based on the number of columns
  let numColumns = headers[0].length;
  let defaultFontSize = 10;
  let maxColumns = 4; // Threshold for when to reduce font size
  let fontSize =
    numColumns > maxColumns ? defaultFontSize - (numColumns - maxColumns) : defaultFontSize;

  // Ensure the font size doesn't go below a certain threshold

  fontSize = Math.max(7, fontSize); // Minimum font size of 7

  // Using jsPDF autotable to add the table to the PDF
  doc.autoTable({
    head: headers, // Table headers (with multiple rows)
    body: body, // Table data
    foot: foot,
    startY: mrgintop, // Start position
    theme: 'grid', // Theme of the table (striped, grid, plain)
    margin: { top: 5, right: 5, bottom: 5, left: 5 },
    styles: {
      fontSize: fontSize, // Dynamically calculated font size
      //cellPadding: 2,
      lineWidth: 0.05,
      lineColor: [0, 0, 0],
      // cellWidth: "wrap",
    },
    headStyles: {
      fillColor: [255, 255, 255], // No background color
      textColor: [0, 0, 0], // Black text color
      lineWidth: 0.05, // Set border width for header cells
      lineColor: [0, 0, 0],
    },
    bodyStyles: {
      fillColor: [255, 255, 255], // No background color
      textColor: [0, 0, 0], // Black text color
    },
    footStyles: {
      fillColor: [255, 255, 255], // No background color
      textColor: [0, 0, 0], // Black text color
      lineWidth: 0.05, // Set border width for header cells
      lineColor: [0, 0, 0],
    },
    showFoot: 'lastPage',
    tableWidth: 'auto', // Allow the table to be responsive and adjust width
  });

  // Save the PDF or open it in a new window
  doc.save(name);
}

// print
function print_html(pageView = 'portrait', print_header, print_date = null, party_name = null) {
  let htmlToPrint = '';
  let divToPrint = document.getElementsByClassName('table_content');
  const newWin = window.open('');
  let godown_name = godownName();
  let ledger_name = getLedgerName();
  let item_name = getStockItemName();
  let group_chart_name = getGroupChartName();
  let stock_group_name = getStockGroupName();
  const d = new Date();
  let on_dated = dateFromTo();
  // Print styles with added handling for table footer
  htmlToPrint = `<style type="text/css" media="print">
                    @page { size: ${pageView == 'No' ? '' : pageView}; }
                    table, td, th {
                        border: 1px solid  #ddd ;
                    }
                    table {
                        width: 100%;
                        border-collapse: collapse;
                    }

                    .hide-btn{
                        visibility: hidden;
                    }
                    .row_style {
                        display: flex;
                        flex-direction: row;
                        flex-basis: 100%;
                        flex: 1;
                    }
                    .box {
                        padding: 15px;
                        width:100%;
                        margin: 5px;
                    }
                    /* Ensures tfoot is placed at the bottom of the last page */
                    tfoot {
                        display: table-row-group;
                    }
                    .text-center{
                      text-align: center !important;
                    }
                    /* Optional font and margin adjustments for print */
                    @media print {
                        .d-print-none{
                          display: none;
                        }

                        /* Hide elements with the .tree-node class when printing */
                        .tree-node {
                            display: none;
                        }

                        /* Show elements with the .show class as table rows when printing */
                        .show {
                            display: table-row;
                        }
                         .text-end{
                            text-align: right;
                         }
                        .text-center{
                            text-align: center !important;
                         }
                        .align-middle{
                          vertical-align: middle;
                        }
                        .td-bold{
                          font-weight: bold;
                        }
                        .d-none{
                          display:none !important;
                        }
                        .font_size_print div span {
                            font-size: 24px !important;
                        }
                        .font_size_print div {
                            margin-bottom: 10px !important;
                        }
                        .payment_font_size_print {
                            font-size: 20px !important;
                        }
                        .payment_font_size_print table {
                            font-size: 20px !important;
                        }
                        .drcr_mp_b_0, .drcr_mp_b_0 p{
                            margin:0;
                            padding:0;
                        }
                      @media print and (max-width: 800px) {
                        ${
                          $('.header_title_challan').length
                            ? `
                            body {
                                font-size: 21px; /* Smaller font for narrower pages */

                            }
                            .header_title_challan{
                                font-size: 25px;
                            }
                            .header_title_challan_name{
                                font-size: 25px;
                            }
                            .hr{
                                height: 3px;
                                background-color: #000000;
                            }
                            .received_by{
                                font-size: 25px;
                                margin-top:80px;
                                margin-bottom:0px;
                                padding-bottom:0px;
                            }
                            .received_by_1{
                                font-size: 25px;
                                margin-top:30px!important;
                                margin-bottom:0px;
                                padding-bottom:0px;
                            }
                            .qr_code {
                                margin:2px!important;
                            }
                            #tableId{
                                font-size: 22px;
                            }
                        `
                            : `
                            body {
                                font-size: 12pt; /* Smaller font for narrower pages */

                            }
                        `
                        }


                      }

                    }

                    @media print {
                      .ledger-list {
                          list-style: none;
                          padding: 0;
                          margin: 10px;
                          border: 1px solid #ddd;
                          font-size: 11px;
                          border-collapse: collapse;
                      }

                      .ledger-list li {
                          display: flex;
                          border-bottom: 1px solid #ccc;
                          align-items: stretch; /* This is key for column height */
                          padding: 0;
                      }

                      .ledger-header {
                          font-weight: bold;
                          background-color: #f9f9f9;
                      }

                      /* Common column styles */
                      .col-particular,
                      .col-remarks {
                          display: flex;
                          align-items: center;        /* Vertical alignment */
                          box-sizing: border-box;
                          /* padding: 4px 6px; */
                          white-space: pre-wrap;      /* Wrap text with preserved line breaks */
                          word-break: break-word;     /* Allow breaking inside long words */
                          min-height: 100%;           /* Ensure full height for border */
                      }
                      .col-amount{
                          display: flex;
                          align-items: center;        /* Vertical alignment */
                          box-sizing: border-box;
                          min-height: 100%;           /* Ensure full height for border */
                      }

                      /* Column-specific styling */
                      .col-particular {
                          flex: 2;
                          border-right: 1px solid #ccc;
                          padding:1px 2px;
                      }

                      .col-amount {
                          flex: 1;
                          justify-content: flex-end;
                          text-align: right;
                          border-right: 1px solid #ccc;
                          padding:1px 2px;
                      }

                      .col-remarks {
                          flex: 2;
                          justify-content: flex-end;
                          text-align: right;
                          padding:1px 2px;
                      }

                      .ledger-row {
                          page-break-inside: avoid;
                      }

                      .no-print,
                      [class*="no-print"] {
                          display: none !important;
                      }
                  }
                </style>`;

  // Creating the HTML header content
  let html_header = `<h4 style="text-align:center;">
                        ${print_date ? $('#current_company_name').text() + '<br>' : ''}
                        ${print_header ? print_header : ''}
                       
                        ${
                          print_date
                            ? `<br>On date:${localStorage.getItem('on_dated')?.trim()}</h4>`
                            : ''
                        }
                    </h4>`;

  if ($('#show_company_name').length) {
    html_header = `${
      $('#print_date').is(':checked')
        ? `<p style="position: absolute; right: 0;padding:0%;margin:0%; font-weight: bold;">Print Date :${d.toDateString()}</p>`
        : ''
    }
                  <h4 style="text-align:center; font-size:18px">
                    ${
                      $('#show_company_name').is(':checked')
                        ? font_size(
                            $('#current_company_name').text(),
                            $('.company_name:checked').val()
                          )
                        : ''
                    }

                    ${
                      $('#show_company_mailing_address').is(':checked')
                        ? font_size(
                            $('#current_company_mailing_address').val(),
                            $('.company_mailing_address:checked').val()
                          )
                        : ''
                    }
                    ${
                      $('#show_report_name').is(':checked')
                        ? font_size(print_header, $('.report_name:checked').val())
                        : ''
                    }
                    ${party_name ? `Party Name: ${ledger_name}<br>` : ''}
                    ${
                      $('.date_print').is(':checked')
                        ? `On date:${localStorage.getItem('on_dated')?.trim()} <br>`
                        : ''
                    }
                    ${
                      godown_name == 'All'
                        ? ''
                        : $('.godown_name_print').is(':checked')
                        ? `Godown Name:${godown_name}<br>`
                        : ''
                    }
                    ${
                      item_name == 'All'
                        ? ''
                        : $('.stock_item_ptint').is(':checked')
                        ? `Stock Item Name:${item_name?.trim()}<br>`
                        : ''
                    }
                    ${
                      ledger_name == '--All--'
                        ? ''
                        : $('.ledger_name_print').is(':checked')
                        ? `Ledger Name:${ledger_name?.trim()} <br>`
                        : ''
                    }
                    ${
                      $('.group_chart_print').is(':checked')
                        ? `Accounts Group:${group_chart_name?.trim()} <br>`
                        : ''
                    }
                    ${
                      $('.stock_group_ptint').is(':checked')
                        ? `Stock Group:${stock_group_name?.trim()} <br>`
                        : ''
                    }
                </h4>`;
  }

  // Write the styles, header, and main content (table) to the new window
  newWin.document.write(htmlToPrint);
  newWin.document.write(html_header);
  newWin.document.write(divToPrint[0].outerHTML); // This includes the table with the footer (tfoot)
  newWin.document.close();
  newWin.location.reload();
  newWin.focus();

  // Print the document
  newWin.print();

  // Close the window after printing
  newWin.close();
}

function generatetr(colCount, data) {
  // Create a new row element for the heading
  const headingRow = document.createElement('tr');

  const headingCell = document.createElement('th');
  headingCell.setAttribute('colspan', colCount);
  headingCell.innerHTML = data;
  headingRow.appendChild(headingCell);
  return headingRow;
}

function exportTableToExcel(name, party_name = null) {
  // Get the table element using the provided ID
  const table = document.getElementById('tableId');
  if (!table) {
    //console.error('Table not found!');
    return;
  }

  const hiddenElements = table.querySelectorAll('.d-none');
  hiddenElements.forEach((el) => el.remove());
  // Clone the table to avoid manipulating the original
  const cloneTable = table.cloneNode(true);
  let data = table.querySelectorAll('tr');
  let TableData = [];
  let colWidths = [];
  let colCount = 0;
  data.forEach((tr, trIndex) => {
    let rowData = [];
    if (TableData.hasOwnProperty(trIndex)) {
      rowData = TableData[trIndex];
    }
    let Elements = tr.querySelectorAll('th,td');
    c = 0;
    tdindex = 0;
    Elements.forEach((thd) => {
      let space = ' '.repeat(getMarginLeft(thd) * 3);
      const cellValue = space + thd.innerText;
      let thdObj = {
        v: cellValue,
        s: {
          alignment: {
            vertical: getVerticalAlign(thd),
            horizontal: getHorizontalAlign(thd),
            //wrapText: thd.querySelector("p.text-wrap") ? true : false
          },
          font: {
            bold: thd.classList.contains('td-bold') || thd.tagName == 'TH' ? true : false,
          },
          // cellPadding: { left: getMarginLeft(thd) },
        },
      };

      if (rowData.hasOwnProperty(tdindex)) {
        while (true) {
          tdindex++;
          if (!rowData.hasOwnProperty(tdindex)) {
            rowData[tdindex] = thdObj;
            break;
          }
        }
      } else {
        rowData[tdindex] = thdObj;
      }

      const rowspan = thd.getAttribute('rowspan') || 1;
      const colspan = thd.getAttribute('colspan') || 1;
      if (colspan > 1) {
        for (i = 1; i < colspan; i++) {
          tdindex++;
          rowData[tdindex] = { v: '' };
        }
      }

      if (rowspan > 1) {
        for (i = 1; i < rowspan; i++) {
          if (TableData.hasOwnProperty(trIndex + i)) {
            TableData[trIndex + i][tdindex] = { v: '' };
          } else {
            TableData[trIndex + i] = [];
            TableData[trIndex + i][tdindex] = { v: '' };
          }
        }
      }
      // Update the column width based on the cell value
      // const columnIndex = tdindex % colWidths.length;
      const currentWidth = colWidths[tdindex] || 0;
      const cellWidth = cellValue.length * 1.2; // Adjust multiplier for optimal width
      colWidths[tdindex] = Math.max(currentWidth, cellWidth);

      tdindex++;
    });
    TableData[trIndex] = rowData; // Add each row of headers as a separate entry
    colCount = Math.max(colCount, tdindex);
  });

  // Retrieve the date from localStorage, handle the case if the value doesn't exist
  const storedDate = localStorage.getItem('on_dated');
  const dateString = storedDate ? storedDate?.trim() : 'Date Not Available';
  const corporationName = $('#current_company_name').text() || '';
  const corporationAddress = $('#current_company_mailing_address').val() || '';

  let header = [];
  if ($('#show_company_name').length) {
    //Company Name
    if ($('#show_company_name').is(':checked')) {
      let tr1 = generatetr(colCount, corporationName);
      cloneTable.insertBefore(tr1, cloneTable.firstChild);
      header.push(makeXlsxHeader(corporationName, $('.company_name:checked').val()));
    }
    //Company Mailing Address
    if ($('#show_company_mailing_address').is(':checked')) {
      let tr1 = generatetr(colCount, corporationName);
      cloneTable.insertBefore(tr1, cloneTable.firstChild);

      header.push(makeXlsxHeader(corporationAddress, $('.company_mailing_address:checked').val()));
    }
    //Report Name
    if ($('#show_report_name').is(':checked')) {
      let tr2 = generatetr(colCount, name);
      cloneTable.insertBefore(tr2, cloneTable.firstChild);

      header.push(makeXlsxHeader(name, $('.report_name:checked').val()));
    }
    //Party Name
    if (party_name) {
      let tr3 = generatetr(colCount, dateString);
      cloneTable.insertBefore(tr3, cloneTable.firstChild);
      header.push(makeXlsxHeader(`Party Name: ${localStorage.getItem('ledger_name')?.trim()}`, 1));
    }
    //Print Date
    if ($('#print_date').is(':checked')) {
      let tr4 = generatetr(colCount, dateString);
      cloneTable.insertBefore(tr4, cloneTable.firstChild);
      header.push(makeXlsxHeader(`On date:${localStorage.getItem('on_dated')?.trim()}`, 1));
    }
    //Godown Name
    if ($('.godown_name_print').is(':checked')) {
      let godown_name = $('.godown_id')
        .find('option:selected')
        .map(function () {
          return $(this).text();
        })
        ?.get()
        ?.join(', ');
      if (godown_name != 'All') {
        let tr4 = generatetr(colCount, `Godown Name:${godown_name}`);
        cloneTable.insertBefore(tr4, cloneTable.firstChild);
        header.push(makeXlsxHeader(`Godown Name:${godown_name}`, 1));
      }
    }

    //stock_item
    if ($('.stock_item_ptint').is(':checked')) {
      let item_name = $('.stock_item_id').find('option:selected').text() || $('.stock_item').val();
      if (item_name != 'All') {
        let tr4 = generatetr(colCount, `Stock Item Name:${item_name?.trim()}`);
        cloneTable.insertBefore(tr4, cloneTable.firstChild);
        header.push(makeXlsxHeader(`Stock Item Name:${item_name?.trim()}`, 1));
      }
    }

    //stock_item
    if ($('.ledger_name_print').is(':checked')) {
      let ledger_name = $('.ledger_id').find('option:selected').text();
      if (ledger_name != '--All--') {
        let tr4 = generatetr(colCount, `Ledger Name:${ledger_name?.trim()}`);
        cloneTable.insertBefore(tr4, cloneTable.firstChild);
        header.push(makeXlsxHeader(`Ledger Name:${ledger_name?.trim()}`, 1));
      }
    }
  } else {
    let tr1 = generatetr(colCount, corporationName);
    let tr2 = generatetr(colCount, name);
    let tr3 = generatetr(colCount, dateString);

    cloneTable.insertBefore(tr3, cloneTable.firstChild);
    cloneTable.insertBefore(tr2, cloneTable.firstChild);
    cloneTable.insertBefore(tr1, cloneTable.firstChild);
    header = [
      [
        {
          v: corporationName,
          s: {
            alignment: { horizontal: 'center', vertical: 'center' },
          },
        },
      ],
      [
        {
          v: name,
          s: {
            alignment: { horizontal: 'center', vertical: 'center' },
          },
        },
      ],
      [
        {
          v: dateString,
          s: {
            alignment: { horizontal: 'center', vertical: 'center' },
          },
        },
      ],
    ];
  }

  TableData = [...header, ...TableData];
  // Create a new workbook and a new worksheet from the cloned table

  const wb = XLSX.utils.book_new();
  const wss = XLSX.utils.table_to_sheet(cloneTable);
  const ws = XLSX.utils.aoa_to_sheet(TableData);
  ws['!merges'] = wss['!merges'];
  ws['!cols'] = colWidths.map((width) => ({ wpx: width * 5 })); // Multiply by a factor for better fit
  console.log(colWidths);
  // Add the worksheet to the workbook
  XLSX.utils.book_append_sheet(wb, ws, 'Sheet1');

  // Ensure the filename has a fallback if name is not provided
  const filename = `${name || 'ExportedData'}.xlsx`;

  // Export the workbook as .xlsx
  XLSX.writeFile(wb, filename, { raw: true });
}

function makeXlsxHeader(name, size) {
  return [
    {
      v: name,
      s: {
        alignment: { horizontal: 'center', vertical: 'center' },
        font: {
          sz: font_size(null, size) - 6,
        },
      },
    },
  ];
}

function font_size(text = '', num = 0) {
  if (text) {
    switch (parseInt(num)) {
      case 1:
        return `<span style="font-size:18px;line-height:normal;">${text}</span><br>`;
      case 2:
        return `<span style="font-size:20px;line-height:normal;">${text}</span><br>`;
      case 3:
        return `<span style="font-size:22px;line-height:normal;">${text}</span><br>`;
      case 4:
        return `<span style="font-size:24px;line-height:normal;">${text}</span><br>`;
      case 5:
        return `<span style="font-size:26px;line-height:normal;">${text}</span><br>`;
      default:
        return `<span style="font-size:20px;line-height:normal;">${text}</span><br>`;
    }
  } else {
    switch (parseInt(num)) {
      case 1:
        return 18;
      case 2:
        return 20;
      case 3:
        return 22;
      case 4:
        return 24;
      case 5:
        return 26;
      default:
        return 20;
    }
  }
}
