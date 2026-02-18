(function ($) {
    function hideResult() {
        $("#shortener-result").addClass("d-none");
    }

    function showMessage(message, isError) {
        var box = $("#shortener-message");
        box.removeClass("d-none alert-danger alert-success");
        box.addClass(isError ? "alert-danger" : "alert-success");
        box.text(message);
    }

    function showResult(shortUrl, qrCodeDataUri) {
        $("#short-link-url").attr("href", shortUrl).text(shortUrl);
        $("#qr-code-image").attr("src", qrCodeDataUri);
        $("#shortener-result").removeClass("d-none");
    }

    $(function () {
        var form = $("#shorten-form");
        var submitButton = $("#shorten-submit");

        form.on("beforeSubmit", function () {
            hideResult();
            $("#shortener-message").addClass("d-none").text("");
            submitButton.prop("disabled", true);

            $.ajax({
                url: form.attr("action"),
                method: "POST",
                data: form.serialize(),
                dataType: "json"
            })
                .done(function (response) {
                    if (!response || response.success !== true) {
                        showMessage(
                            (response && response.message) ? response.message : "Не удалось сократить ссылку.",
                            true
                        );
                        return;
                    }

                    showMessage("Ссылка успешно создана.", false);
                    showResult(response.shortUrl, response.qrCode);
                })
                .fail(function () {
                    showMessage("Ошибка сервера. Попробуйте позже.", true);
                })
                .always(function () {
                    submitButton.prop("disabled", false);
                });

            return false;
        });

        form.on("submit", function (event) {
            event.preventDefault();
        });
    });
})(jQuery);
