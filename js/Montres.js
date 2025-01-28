function showCategory(value) {
    document.getElementById('homme').style.display = value == 0 ? 'grid' : 'none';
    document.getElementById('femme').style.display = value == 1 ? 'grid' : 'none';
}