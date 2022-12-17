

function showAlert(text) {
    let ab = document.getElementById('alert')

    ab.innerHTML = text;

    ab.classList.add('active')

    setTimeout(() => {
        ab.classList.remove('active')
    }, 3000)
}