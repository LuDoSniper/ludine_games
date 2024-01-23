function hide(){
    document.getElementById('login_container').style.display = 'none';
    document.getElementById('overlay').style.display = 'none';
}

function show(){
    document.getElementById('login_container').style.display = 'flex';
    document.getElementById('overlay').style.display = 'block';
}

hide();