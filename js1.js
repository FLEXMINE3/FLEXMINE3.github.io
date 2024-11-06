function updatePrice() {
  // Находим select по имени в DOM.
  let s = document.getElementsByName("prodType");
  let select = s[0];
  let price = 0;
  let amount = document.getElementById("amount").value;
  let prices = getPrices();
  let priceIndex = parseInt(select.value) - 1;
  if (priceIndex >= 0) {
    price = prices.prodTypes[priceIndex];
  }
  
  // Скрываем или показываем радиокнопки.
  let radioDiv = document.getElementById("radios");
  radioDiv.style.display = (select.value == "3" ? "block" : "none");
  
  // Смотрим какая товарная опция выбрана.
  let radios = document.getElementsByName("prodOptions");
  radios.forEach(function(radio) {
    if (radio.checked) {
      let optionPrice = prices.prodOptions[radio.value];
      if (optionPrice !== undefined) {
        price += optionPrice;
      }
    }
  });

  // Скрываем или показываем чекбоксы.
  let checkDiv = document.getElementById("checkboxes");
  checkDiv.style.display = (select.value == "1" ? "none" : "block");
  checkDiv.style.display = (select.value == "3" ? "none" : "block");
  


  // Смотрим какие товарные свойства выбраны.
  let checkboxes = document.querySelectorAll("#checkboxes input");
  checkboxes.forEach(function(checkbox) {
    if (checkbox.checked) {
      let propPrice = prices.prodProperties[checkbox.name];
      if (propPrice !== undefined) {
        price += propPrice;
      }
    }
  });
  
  let prodPrice = document.getElementById("prodPrice");
  prodPrice.innerHTML = price*Math.abs(amount) + " рублей";
}

function getPrices() {
  return {
    prodTypes: [100, 180, 170],
    prodOptions: {
      option1:50,
      option2: 40,
      option3: 70,
    },
    prodProperties: {
      prop1: 20,
      prop2: 25,
    }
  };
}

window.addEventListener('DOMContentLoaded', function (event) {
  // Скрываем радиокнопки.
  let radioDiv = document.getElementById("radios");
  radioDiv.style.display = "none";
  
  // Находим select по имени в DOM.
  let s = document.getElementsByName("prodType");
  let select = s[0];
  // Назначаем обработчик на изменение select.
  select.addEventListener("change", function(event) {
    let target = event.target;
    console.log(target.value);
    updatePrice();
  });
  
  // Назначаем обработчик радиокнопок.  
  let radios = document.getElementsByName("prodOptions");
  radios.forEach(function(radio) {
    radio.addEventListener("change", function(event) {
      let r = event.target;
      console.log(r.value);
      updatePrice();
    });
  });

    // Назначаем обработчик чекбокса.  
  let checkboxes = document.querySelectorAll("#checkboxes input");
  checkboxes.forEach(function(checkbox) {
    checkbox.addEventListener("change", function(event) {
      let c = event.target;
      console.log(c.name);
      console.log(c.value);
      updatePrice();
    });
  });

  updatePrice();
});
