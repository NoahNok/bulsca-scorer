

function showAlert(text) {
    let ab = document.getElementById('alert')

    ab.innerHTML = text;

    ab.classList.remove('success')

    ab.classList.add('active')

    setTimeout(() => {
        ab.classList.remove('active')
    }, 3000)
}

function showSuccess(text) {
    let ab = document.getElementById('alert')

    ab.innerHTML = text;

    ab.classList.add('success')

    setTimeout(() => {
        ab.classList.add('active')
        setTimeout(() => {
            ab.classList.remove('active')
        }, 3000)
    }, 50)


}