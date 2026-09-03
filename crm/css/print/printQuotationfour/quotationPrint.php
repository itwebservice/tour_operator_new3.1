<?php
include_once('../../../model/model.php');
header("Content-type: text/css");
?>

.landingPageTop {
position: relative;
}
h1.landingpageTitle {
position: absolute;
margin: 0;
width: 100%;
top: 5px;
left: 0;
font-size: 36px;
text-transform: capitalize;
line-height: 32px;
color: #000000 !important;
background-color: rgba(255, 255, 255, 0.5) !important;
-webkit-print-color-adjust: exact;
text-align: center;
font-weight: 400;
padding: 25px 15px;
}
span.landingPageId {
position: absolute;
right: 30px;
bottom: 30px;
color: #2c3642 !important;
background-color: rgba(255, 255, 255, 0.6) !important;
-webkit-print-color-adjust: exact;
padding: 10px 15px;
font-weight: 500;
font-size: 13px;
border: 1px solid <?= $theme_color ?>;
border-radius: 8px;
}

.landigPageCustomer {
margin-bottom : 6px;
}
h3.customerFrom {
font-size: 16px;
margin-top: 0px;
margin-bottom: 20px;
line-height: 36px;
border-bottom: 1px solid <?= $theme_color ?>;
text-transform: capitalize;
color: <?= $theme_color ?> !important;
-webkit-print-color-adjust: exact;
font-weight: 400;
font-size: 24px;
}
.landigPageCustomer span{
line-height: 28px;
}
.landigPageCustomer i{
min-width : 30px;
}
.landigPageCustomer i:before {
color: <?= $theme_color ?> !important;
-webkit-print-color-adjust: exact;
}
span.generatorName {
font-size: 14px;
font-weight: 500;
line-height: 26px;
display: block;
margin-top: 20px;
}

.detailBlock {
display: inline-block;
width: 110px;
margin-top: -80px;
}
.detailBlockIcon {
background-color: <?= $theme_color ?> !important;
-webkit-print-color-adjust: exact;
font-size: 28px;
width: 60px;
height: 60px;
line-height: 60px;
text-align: center;
transform: rotate(45deg);
margin: 30px auto;
border-radius: 10px;
}
.detailBlockIcon i {
font-size: 30px;
transform: rotate(-45deg);
}
.detailBlockIcon i:before {
color: #fff !important;
-webkit-print-color-adjust: exact;
}
h3.contentValue {
color: #2c3642 !important;
-webkit-print-color-adjust: exact;
margin: 0;
font-size: 16px;
line-height: 24px;
}
h3.highlightContentValue{
color: #a10606 !important;
-webkit-print-color-adjust: exact;
margin: 0;
font-size: 15px;
line-height: 24px;
font-weight: 500;
}
span.contentLabel {
color: #2c3642 !important;
-webkit-print-color-adjust: exact;
text-transform: uppercase;
font-size: 10px;
line-height: 16px;
}

.travsportInfoBlock {
border: 1px solid <?= $theme_color ?>;
position: relative;
width: 100%;
margin-left: 0;
padding: 8px 6px;
box-sizing: border-box;
overflow: hidden;
}
.transportIcon {
background-color: <?= $theme_color ?> !important;
-webkit-print-color-adjust: exact;
position: absolute;
width: 160px;
height: 160px;
border-radius: 50%;
left: -70px;
top: -15px;
}
.transportIcon img {
width: 100px;
position: absolute;
top: calc(50% - 50px);
left: calc(50% - 50px);
}
.transportDetails {
width: 100%;
margin-left: 0;
position: relative;
min-height: auto;
display: block;
padding: 0;
box-sizing: border-box;
}
.transportDetailsPanel .table-responsive {
width: 100%;
max-width: 100%;
overflow: hidden;
margin: 0;
}
.transportDetailsPanel .quotation-table-wrap {
width: 100%;
max-width: 100%;
overflow: hidden;
margin: 0;
}
.transportDetailsPanel {
width: 100%;
max-width: 100%;
box-sizing: border-box;
overflow: hidden;
}
.transportDetailsPanel table.tableTrnasp,
.transportDetailsPanel table.quotation-doc-table {
width: 100% !important;
max-width: 100% !important;
table-layout: fixed !important;
border-collapse: collapse !important;
border-spacing: 0 !important;
margin: 0 !important;
}
.transportDetailsPanel table.tableTrnasp th,
.transportDetailsPanel table.tableTrnasp td,
.transportDetailsPanel table.quotation-doc-table th,
.transportDetailsPanel table.quotation-doc-table td {
padding: 4px 2px !important;
padding-left: 2px !important;
text-align: left !important;
vertical-align: top !important;
word-wrap: break-word !important;
overflow-wrap: anywhere !important;
word-break: break-word !important;
white-space: normal !important;
font-size: 9px !important;
font-weight: 400 !important;
color: #22262E !important;
border: 0 !important;
border-top: 0 !important;
border-bottom: 0 !important;
box-sizing: border-box !important;
max-width: 0 !important;
min-width: 0 !important;
overflow: hidden;
}
.transportDetailsPanel table.tableTrnasp tr.table-heading-row th,
.transportDetailsPanel table.quotation-doc-table tr.table-heading-row th {
font-size: 7px !important;
font-weight: 600 !important;
text-transform: uppercase;
background-color: #f4eeee !important;
-webkit-print-color-adjust: exact;
}
.transportDetailsPanel table.tableTrnasp th:after,
.transportDetailsPanel table.tableTrnasp th:before,
.transportDetailsPanel table.quotation-doc-table th:after,
.transportDetailsPanel table.quotation-doc-table th:before {
display: none !important;
content: none !important;
width: 0 !important;
height: 0 !important;
}
.pageSectionInner {
position: relative !important;
width: 100% !important;
left: auto !important;
top: auto !important;
}
.transportDetails .transportDetailsHalf {
display: table;
position: absolute;
top: 0;
min-height: 130px;
width: 50%;
text-align: center;
}
.transportDetailsHalf.leftHalf {
left: 0;
}
.transportDetailsHalf.rightHlf {
right: 0;
}
.transportDetailsHalf .transportDetailText{
display: table-cell;
vertical-align: middle;
}
.transportDetailsHalf .transportDetailText p{
margin-bottom: 5px;
}

section.print_single_itinenary {
list-style-type: none;
width: 95%;
border: 1px solid <?= $theme_color ?>;
margin: 30px 0;
height: 100%;
}
section.print_single_itinenary.leftItinerary{
float: right;
border-right: 0;
}
section.print_single_itinenary.rightItinerary{
float: left;
border-left: 0;
}
.itneraryImg {
width: 40%;
padding: 0 25px 0 15px;
}
.leftItinerary .itneraryImg{ float: left; }
.rightItinerary .itneraryImg{ float: right; }
.itneraryImgblock {
position: relative;
z-index: 0;
}
.itneraryImgblock:after {
position: absolute;
content: '';
width: 100%;
height: 100%;
background-color: <?= $theme_color ?> !important;
-webkit-print-color-adjust: exact;
left: 10px;
top: 10px;
}
.itneraryImg img {
margin-top: -30px;
position: relative;
margin-bottom: 30px;
z-index: 1;
}
.itneraryDayAccomodation {
margin-bottom: 20px;
}
.itneraryDayAccomodation span {
display: block;
margin: 10px 0;
}
.itneraryDayAccomodation span i:before {
color: <?= $theme_color ?> !important;
font-size: 18px;
margin-right: 10px;
width: 16px;
display: inline-block;
}
.itneraryText {
padding: 15px;
width:750px;
text-align: initial;
}
.itneraryDayInfo {
margin-bottom: 10px;
}
.itneraryDayInfo i:before {
font-size: 26px;
color: <?= $theme_color ?> !important;
margin-right: 10px;
}
.itneraryDayInfo span {
font-weight: 500;
}
.itneraryDayPlan p {
font-size: 11px;
max-height: 280px;
overflow: hidden;
}
h3.incexTitle {
color: <?= $theme_color ?> !important;
}
.incluExcluTermsTabPanel.inclusions {
text-align: left;
margin-top: 20px;
padding: 30px 20px 50px 0;
<!-- border-right: 4px solid <?= $theme_color ?>; -->
position: relative;
}
.incluExcluTermsTabPanel.inclusions:before {
position: absolute;
<!-- content: ''; -->
width: 0;
height: 0;
border-style: solid;
border-width: 0 15px 30px 15px;
border-color: transparent transparent <?= $theme_color ?> transparent;
top: -30px;
right: -17px;
}
.incluExcluTermsTabPanel.inclusions:after {
position: absolute;
<!-- content: ''; -->
height: 20px;
width: 20px;
<!-- border: 4px solid <?= $theme_color ?>;
    border-radius: 50%; -->
right: -11.5px;
bottom: -20px;
}
.incluExcluTermsTabPanel.exclusions {
margin-top: 10px;
padding: 0 0 50px 20px;
<!-- border-left: 4px solid <?= $theme_color ?>; -->
position: relative;
}
.incluExcluTermsTabPanel.exclusions:before {
position: absolute;
<!-- content: ''; -->
width: 0;
height: 0;
border-style: solid;
border-width: 30px 15px 0 15px;
border-color: <?= $theme_color ?> transparent transparent transparent;
bottom: -30px;
left: -16px;
}
.incluExcluTermsTabPanel.exclusions:after {
position: absolute;
<!-- content: ''; -->
height: 20px;
width: 20px;
<!-- border: 4px solid <?= $theme_color ?>;
    border-radius: 50%; -->
left: -11.5px;
top: -20px;
}
.incluExcluTermsTabPanel pre.real_text ul li {
list-style-position: inside;
}
.incluExcluTermsTabPanel pre.real_text ul {
padding: 0;
}

h3.endingPageTitle {
margin-bottom: 30px;
color: <?= $theme_color ?> !important;
}
.passengerPanel .icon {
width: 180px;
margin: 0 auto;
background-color: <?= $theme_color ?> !important;
-webkit-print-color-adjust: exact;
padding: 60px;
border-radius: 50%;
}
.passengerPanel h4 {
position: absolute;
width: 100%;
text-align: center;
bottom: 30px;
left: 0;
color: #fff !important;
}


.constingBankingPanelRow{
background-color: <?= $theme_color ?> !important;
-webkit-print-color-adjust: exact;
min-height: 783px;

}
.constingBankingPanel.constingPanel {
position: relative;
}
.constingBankingPanel.constingPanel:after {
position: absolute;
content: '';
width: 90%;
height: 4px;
background-color: #fff !important;
-webkit-print-color-adjust: exact;
bottom: 0;
left: 5%;
border-radius: 25px;
}
h3.costBankTitle.text-center {
color: #fff !important;
margin: 30px 0;
text-transform: capitalize;
}
.constingBankingPanel .icon img {
width: 50px;
margin: 0 auto 10px;
}
.constingBankingPanel h4 {
color: #fff !important;
line-height: 30px;
}
.constingBankingPanel p {
font-size: 14px;
color: #fff !important;
}

.companyLogo {
height: 175px;
max-height: 175px;
}
.companyLogo img {
margin: 20px 0 0 0;
max-height: 175px;
}
.companyContactDetail h3 {
color: <?= $theme_color ?> !important;
margin-bottom: 50px;
text-transform: uppercase;
}
.contactBlock {
width: 50%;
margin: 0 auto 40px;
}
.contactBlock i {
margin-bottom: 10px;
}
.contactBlock i:before {
color: <?= $theme_color ?> !important;
font-size: 36px;
}

.table td, .modal table.dataTable td {
border: 0 !important;
<!-- font-size: 10px ; -->
font-weight: 400;
color: #22262E;
padding: 15px 8px 10px 28px !important;
}
.transportDetailsPanel .table td,
.transportDetailsPanel .table th {
padding: 8px 6px !important;
}


.transportDetails_costing {
width: 100%;
position: relative;
display : inline-block;
}
.transportDetails_costing table tr th {
color: #000000 !important;
background-color: #d2d2d2 !important;
text-align:left !important;
}
.transportDetails_costing table tr td {
color: #000000 !important;
text-align:left !important;
}
.vitinerary_div h6{
color: black !important;
}
.vitinerary_div a{
text-decoration:underline !important;
}

@media print {
table tr.table-heading-row th, table tfoot tr td{
-webkit-print-color-adjust: exact;
}
}

/* FIT quotation doc tables - must be last to override bootstrap/app.php */
.transportDetailsPanel .quot-table-wrap {
width: 100% !important;
max-width: 100% !important;
overflow: hidden !important;
margin: 0 !important;
padding: 0 !important;
}
.transportDetailsPanel table.quot-fit-doc-table {
width: 100% !important;
max-width: 100% !important;
table-layout: fixed !important;
border-collapse: collapse !important;
margin: 0 !important;
}
.transportDetailsPanel table.quot-fit-doc-table thead tr.table-heading-row {
background-color: #f4eeee !important;
-webkit-print-color-adjust: exact;
}
.transportDetailsPanel table.quot-fit-doc-table th,
.transportDetailsPanel table.quot-fit-doc-table td {
padding: 4px 3px !important;
padding-left: 3px !important;
padding-right: 3px !important;
font-size: 10px !important;
font-family: Poppins, sans-serif !important;
word-wrap: break-word !important;
overflow-wrap: anywhere !important;
word-break: break-word !important;
white-space: normal !important;
border: 0 !important;
border-top: 0 !important;
border-bottom: 0 !important;
box-sizing: border-box !important;
max-width: 0 !important;
min-width: 0 !important;
overflow: hidden !important;
vertical-align: middle !important;
line-height: 1.3 !important;
margin: 0 !important;
text-indent: 0 !important;
}
body .transportDetailsPanel table.quot-fit-doc-table > thead > tr > th,
body .transportDetailsPanel table.quot-fit-doc-table > tbody > tr > td {
padding-left: 3px !important;
padding-right: 3px !important;
}
.transportDetailsPanel table.quot-fit-doc-table tr.table-heading-row th {
font-size: 10px !important;
font-family: Poppins, sans-serif !important;
font-weight: 600 !important;
text-transform: uppercase;
background-color: #f4eeee !important;
-webkit-print-color-adjust: exact;
}
.transportDetailsPanel table.quot-fit-doc-table th:after,
.transportDetailsPanel table.quot-fit-doc-table th:before {
display: none !important;
content: none !important;
width: 0 !important;
height: 0 !important;
padding: 0 !important;
margin: 0 !important;
left: 0 !important;
position: static !important;
}