var idUser = $("#iduser").data("iduser");

//Action du bouton "Add"
$("#addItemBtn").click(function () {
  AddItem();
});

//Lorsqu'on appuie sur Enter, ajoute un item
$("#newItem").on("keyup", function (e) {
  if (e.key === "Enter" || e.keyCode === 13) {
    AddItem();
  }
});

//Ajout d'un item
function AddItem() {
  var text = $("#newItem").val();
  $.ajax({
    url: "/create",
    type: "POST",
    data: { text: text, iduser: idUser },
    success: function (idLastItem) {
      AddItemToList(idLastItem, text, false);
    },
    error: function () {
      alert("not ok");
    }
  });
}
//Action du bouton supprimer
$(document).on("click", ".itemlist__item__deletebtn", function () {
  //id de l'item a supprimer
  var idItem = $(this).data("id");
  OpenModalDeleteItem(idItem);
});

//Ouverture de la modal de suppression
function OpenModalDeleteItem(idItem) {
  $(".modalsuppression").addClass("modalsuppression--show");
  $("#overlay").css("display", "block");
  $("#confirmsuppression")
    .unbind("click")
    .click(function () {
      DeleteItem(idItem);
    });

  $("#closemodalsuppression").click(function () {
    $(".modalsuppression").removeClass("modalsuppression--show");
    $("#overlay").css("display", "none");
  });
}

//Suppression d'un item
function DeleteItem(idItem) {
  //on supprime l'item de la liste
  $("[data-id=" + idItem + "]").remove();
  $(".modalsuppression").removeClass("modalsuppression--show");
  $("#overlay").css("display", "none");
  $.ajax({
    url: "/delete",
    type: "POST",
    data: { id: idItem },
    success: function () {},
    error: function () {
      alert("not ok");
    }
  });
}

//Action bouton modifier un item
$(document).on("click", ".itemlist__item__editbtn", function () {
  var idItem = $(this).data("id");
  var inputText = $("input[data-id=" + idItem + "]");
  if (inputText.prop("disabled")) {
    inputText.prop("disabled", false);
    inputText.removeClass("itemlist__item--disabled");
    inputText.removeClass("itemlist__item--done");
  } else {
    inputText.prop("disabled", true);
    EditItem(idItem);
    inputText.addClass("itemlist__item--disabled");
  }
});

//Lorsqu'on appuie sur Enter, valide la modification de l'item
$(document).on("keyup", ".itemlist__item", function (e) {
  var idItem = $(this).data("id");
  if (e.key === "Enter" || e.keyCode === 13) {
    EditItem(idItem);
  }
});

//Modification d'un item
function EditItem(idItem) {
  var inputText = $("input[data-id=" + idItem + "]");
  var text = inputText.val();
  inputText.prop("disabled", true);
  inputText.addClass("itemlist__item--disabled");
  $.ajax({
    url: "/edit",
    type: "POST",
    data: { id: idItem, text: text },
    success: function (isdone) {
      if (isdone) {
        inputText.addClass("itemlist__item--done");
      } else {
        inputText.removeClass("itemlist__item--done");
      }
    },
    error: function () {
      alert("error modification");
    }
  });
}

//Mettre un item en "done"
$(document).on("click", ".checkboxdone", function () {
  var idItem = $(this).data("iditem");
  var isDone = $(this).is(":checked");
  SetItemAsDone(idItem, isDone);
});

function SetItemAsDone(idItem, isDone) {
  var inputText = $("input[data-id=" + idItem + "]");
  if (isDone) {
    inputText.addClass("itemlist__item--done");
  } else {
    inputText.removeClass("itemlist__item--done");
  }
  $.ajax({
    url: "/setisdone",
    type: "POST",
    data: { id: idItem, isdone: isDone },
    success: function () {},
    error: function () {
      alert("not ok setisdone");
    }
  });
}

$("#showAll").on("click", function () {
  $.ajax({
    type: "POST",
    url: "/showall",
    dataType: "json",
    success: function (listItem) {
      $("#itemlist").empty();
      for (var i in listItem) {
        AddItemToList(listItem[i].id, listItem[i].text, listItem[i].isDone);
      }
    }
  });
});

$("#showActive").on("click", function () {
  $.ajax({
    type: "POST",
    url: "/showactive",
    dataType: "json",
    success: function (listItem) {
      $("#itemlist").empty();
      for (var i in listItem) {
        AddItemToList(listItem[i].id, listItem[i].text, listItem[i].isDone);
      }
    }
  });
});

$("#showCompleted").on("click", function () {
  $.ajax({
    type: "POST",
    url: "/showcompleted",
    dataType: "json",
    success: function (listItem) {
      $("#itemlist").empty();
      for (var i in listItem) {
        AddItemToList(listItem[i].id, listItem[i].text, listItem[i].isDone);
      }
    }
  });
});

function AddItemToList(id, text, isDone) {
  $("#itemlist").append(
    '<hr data-id="' +
      id +
      '" class="m-0">' +
      '<div data-id="' +
      id +
      '" class="itemlist__divitems m-0 d-flex align-items-center">' +
      '<input data-iditem="' +
      id +
      '" type="checkbox" ' +
      (isDone ? "checked " : "") +
      'class="mr-2 checkboxdone">' +
      '<input data-id="' +
      id +
      '" class="itemlist__item itemlist__item--disabled ' +
      (isDone ? "itemlist__item--done" : "") +
      '" value="' +
      text +
      '" disabled>' +
      '<div class="d-flex w-100 justify-content-end align-items-center">' +
      '<img data-id="' +
      id +
      '" class="itemlist__item__editbtn" src="images/edit.png">' +
      '<button data-id="' +
      id +
      '" class="itemlist__item__deletebtn">' +
      "<span>x</span>" +
      "</button>" +
      "</div>" +
      "</div>"
  );
}
