$(document).ready(init);

function init () {
    
    let alert_closes = $('.toast .btn-clear');
    alert_closes.each(function() {
        this.onclick = closeAlert;
    });

}

function closeAlert(e) {
    let elem = e.target;
    let alert = $(elem).closest('.toast');
    alert.remove();
}