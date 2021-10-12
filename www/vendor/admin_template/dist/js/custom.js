function flashMessage(message, type) {
    $.notify({
        message: message
    },{
        type: type,
        delay: 2500,
        animate: {
            enter: 'animated fadeInDown',
            exit: 'animated fadeOutUp'
        },
        placement: {
            from: "top",
            align: "center"
        },
    });
}