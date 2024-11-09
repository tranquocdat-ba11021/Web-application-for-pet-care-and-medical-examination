function togglePriceField() {
    var type = document.getElementById('type').value;
    var priceField = document.getElementById('price_field');
    if (type == '2') { // Dịch vụ
        priceField.style.display = 'block';
        document.getElementById('price').required = true;
    } else { // Khám bệnh
        priceField.style.display = 'none';
        document.getElementById('price').required = false;
    }
}

function validateForm() {
    var type = document.getElementById('type').value;
    var price = document.getElementById('price').value;
    if (type == '2' && price.trim() == '') {
        alert('Price is required for services.');
        return false;
    }
    return true;
}
