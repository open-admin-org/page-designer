window.imagesSetContent = function(data,current_content){
    current_content.innerHTML = '<img src="/storage/'+data.images[0]+'">';
}

window.movieSetContent = function(data,current_content){
    current_content.innerHTML = '<img src="/storage/'+data.thumb+'"><div class="icon icon-play"></div>';
}

window.inline_gallerySetContent = function(data,current_content){
    images = JSON.parse(data.images);
    current_content.innerHTML = '<img src="/storage/'+images[0]+'">';
}

window.textSetContent = function(data,current_content){
    current_content.innerHTML = data.body;
}