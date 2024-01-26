function hide(id){
    document.getElementById(id).style.display = 'none';
}

function show(id){
    document.getElementById(id).style.display = 'flex';
}

function hidedivs(ids){
    console.log(ids);
    ids.forEach(id => {
        document.getElementById("game_id-" + id.ID).style.display = 'none';
    });
}