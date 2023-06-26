<style>
.tooltips {
    position: relative;
    display: inline-block;
}
.tooltips .tooltiptext {
    visibility: hidden;
    width: 120px;
    background-color: black;
    color: #fff;
    text-align: center;
    border-radius: 6px;
    padding: 5px 0;

    position: absolute;
    z-index: 1;
    bottom: 120%;
    left: 50%;
    margin-left: -60px;
}

.tooltips:hover .tooltiptext {
    visibility: visible;
}
</style>