function Converter() {

    this.progress = 0;
    this.status = '';
    this.progressBar = null;
    this.progressBarScale = null;
    this.progressBarLabel = null;
    this.id = '';

    this.timer = {
        draw_progress: null
    };

    /**
     * Дергаем контроллер progress
     * он возвращает прогресс в процентах
     */
    this.request_progress = function() {
        var self = this;
        var d = new Date();
        var item = {
            id: this.id
        };
        $.post(
            "/progress?now="+d.getTime(),
            item,
            function(data) {
                self.progress = data.progress;
                self.status = data.status;
                self.render_progress();
            },
            "json"
        );
    };

    /**
     * Собственно отрисовка прогресса
     */
    this.render_progress = function() {
        this.progressBarScale.css('width', this.progress + '%');
        this.progressBarLabel.text(this.progress + '% complete');
        if (this.status == 'success') {
            clearInterval(this.timer.draw_progress);
            window.location.href = '/slider?id='+this.id;
        }
    };

    /**
     * Дергаем контроллер convert
     * Если сейчас данный файл в процессе обработки -
     * т.е. мы нажали F5 - то контроллер вернет статус in_progress
     *
     * Останавливать Таймер
     * необходимо в рендере, но туда должно прийти это состояние
     * или вообще всегда останавливать в рендере.
     *
     * если мы запустили первый раз - то контроллер будет работать долго
     * пока не трансформирует файл и вернет в результате success
     */
    this.request_converter = function() {
        var self = this;
        var d = new Date();
        var item = {
            id: this.id
        };
        $.post(
            "/convert?now="+d.getTime(),
            item,
            function(data) {
                alert(data.result);
            },
            "json"
        );
    };

    /**
     * Инициализация
     * 1) получим доступ к контролам на странице
     * 2) запустим таймер проверки прогресса работы
     * 3) дернем контроллер convert
     */
    this.init = function() {
        var self = this;
        this.progressBar = $("#ProgressBar");
        this.progressBarScale = $(".progress-bar", this.progressBar);
        this.progressBarLabel = $("#ProgressLabel");
        this.id = this.progressBar.attr("data-id");
        this.timer.draw_progress = setInterval(function() { self.request_progress.call(self) }, 300);
        this.request_converter();
    };

}

$.ajaxSetup({cache: false});
$(document).ready(function() {
    /**
     * Ищем на странице шкалу прогресса
     * и если найдем - то запускаем конвертацию
     * и проверку прогресса
     */
    var n = $("#ProgressBar").size();
    if (n > 0) {
        var converter = new Converter();
        converter.init();
    }
});