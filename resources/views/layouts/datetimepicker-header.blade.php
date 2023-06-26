<link rel="stylesheet" href="{{ asset('assets/datetimepicker/css/bootstrap-datetimepicker-4.17.47.css') }}">
<style>
@font-face{
    font-family:'Glyphicons Halflings';
    src:url({{asset('assets/fonts/glyphicons-halflings-regular.woff')}}) format('woff'),url({{asset('assets/fonts/glyphicons-halflings-regular.ttf')}}) format('truetype')
}
.glyphicon{
    position:relative;
    top:1px;
    display:inline-block;
    font-family:'Glyphicons Halflings';
    font-style:normal;
    font-weight:400;
    line-height:1;
    -webkit-font-smoothing:antialiased;
    -moz-osx-font-smoothing:grayscale
}
.glyphicon-time:before {content:"\e023"}
.glyphicon-chevron-left:before{content:"\e079"}
.glyphicon-chevron-right:before{content:"\e080"}
.glyphicon-calendar:before {content: "\e109"}
.glyphicon-chevron-up:before {content: "\e113"}
.glyphicon-chevron-down:before {content: "\e114"}
.collapse.in {
    display: block;
    visibility: visible
}
.table-condensed>thead>tr>th,
.table-condensed>tbody>tr>th,
.table-condensed>tfoot>tr>th,
.table-condensed>thead>tr>td,
.table-condensed>tbody>tr>td,
.table-condensed>tfoot>tr>td {
    padding: 5px
}
</style>