$(function () {
    initElectronControls();
})

const isElectron = () => {
    return window.isElectron
}

const initElectronControls = () => {
    if (isElectron()) {
        generalPDFs()
        electronShutdown()
        electronReboot()
        electronMinimize()
    }
}

const electronShutdown = () => {
    $('body').append('<i class="material-icons app-exit" data-toggle="modal" data-target="#electron-shutdown-modal">power_settings_new</i>').addClass('e')
    $(document).on('click', '#app-exit', function () {
        window.ipcRenderer.send('app-exit', 'kill me')
    })
}

const electronMinimize = () => {
    $('body').append('<i class="material-icons app-exit minimize" data-toggle="modal" data-target="#electron-minimize-modal">minimize</i>').addClass('e')

    $(document).on('click', '#app-minimize', function () {
        const password = $('#electron-minimize-code').val()
        if(!password) {
            electronToast(0, 'Please enter auth code first');
            return
        }
        window.ipcRenderer.send('app-minimize', password)
        $('#electron-minimize-code').val('')
    })
    window.ipcRenderer.on('error', (event, msg) => {
        electronToast(0, msg);
    })
}

const electronReboot = () => {
    $('body').append('<i class="material-icons app-exit reboot" data-toggle="modal" data-target="#electron-reboot-modal">refresh</i>').addClass('e')
    $(document).on('click', '#app-reboot', function () {
        window.ipcRenderer.send('app-reboot', 'reboot me')
    })
}

const electronToast = (kind, message) => {
    var errorToast = {'position':'top','align':'right', 'duration': 4000, 'class': "bg-danger"}
    var successToast = {'position':'top','align':'right', 'duration': 4000, 'class': "bg-primary"}
    switch(kind){
        case 0:
            style = errorToast;
            break;
        default:
            style = successToast;
    }
    $.Toast(message, style);
    return
}

const generalPDFs = () => {
    $(document).on('click', 'table a[target="_blank"]', function (event) {
        const url = $(this).attr('href')
        event.preventDefault()
        embedpdf(url, '.main-content')
        return
    })
}

const embedpdf = (url, to) => {
    $('.user-control').css({'background' : '#fff'})
    $('body').css({'background-color': 'rgb(82, 86, 89)'})
    const el = `
        <style>
            #p-g-back {
                color: #fff;
                background: #ff7c00 !important;
                position: fixed;
                padding: 20px;
                cursor: pointer;
                bottom: 20px;
                right: 20px;
                z-index: 2;
                border-radius: 50%;
            }
        </style>
        <div class="material-icons" id="p-g-back" onclick="window.location.reload()">refresh</div>
        <div class='embed-container'>
            <iframe src='${url}' frameborder='0' allowfullscreen></iframe>
        </div>
        `;
    $(to).addClass('p-0').html(el)
    return
}