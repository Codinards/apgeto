// localStorage.clear();
const TODO_LOCAL_KEY = "todo-list";
const taskInput = document.querySelector(".task-input input[name=task]");
const AddCategorySelect = document.querySelector(
  ".task-input select[name=category]"
);
const categoryInput = document.querySelector(
  ".task-input input[name=add_category]"
);
const filters = document.querySelectorAll(".filters span");
const btnClear = document.querySelector(".clear-btn");
const taskBox = document.querySelector(".task-box");
let todos = JSON.parse(localStorage.getItem(TODO_LOCAL_KEY)) || [];
let categories;
let EditedTaskId;
let isUpdating = false;
let filterIds = [];

const createOptions = (categories) => {
  AddCategorySelect.innerHTML = "";
  categories.forEach((element, index) => {
    AddCategorySelect.innerHTML += `<option value="${element}" data-id=${index}>${
      element == "DEFAULT" ? "" : element
    }</option>`;

    if (
      filterIds.find((index) => index == element) == undefined &&
      element != "DEFAULT"
    ) {
      filterIds.push(element);
      document.querySelector(".filters").appendChild(
        (() => {
          const span = document.createElement("span");
          span.id = element;
          span.innerHTML = element;
          span.addEventListener("click", (e) => {
            toggleFilter(span);
          });
          return span;
        })()
      );
    }
  });
};

filters.forEach((element) => {
  element.addEventListener("click", (e) => {
    // document.querySelector(".filters span.active").classList.remove("active");
    // element.classList.add("active");
    // showTodos(element.id);
    toggleFilter(element);
  });
  filterIds.push(element.id);
});

function toggleFilter(element) {
  document.querySelector(".filters span.active").classList.remove("active");
  element.classList.add("active");
  showTodos(element.id);
}

function showTodos(filter) {
  let li = "";
  todos.forEach((todo, id) => {
    let isCompleted = todo.status == "completed" ? "checked" : "";
    if (filter == todo.status || filter == "all" || filter == todo.category) {
      li += `
  <li class="task" ondblclick="updateTask(${id}, '${todo.name}')">
    <label for="${id}">
        <input onClick="updateStatus(this)" type="checkbox" name="${id}" id="${id}" ${isCompleted}>
        <p class='${isCompleted}'>${todo.name}</p>
    </label>
    <div class="settings">
        <i onclick="showMenu(this)" style="font-weight: bolder;">...</i>
        <ul class="task-menu">
            <li onclick="updateTask(${id}, '${todo.name}')"><i class="fa fa-pen"></i>Edit</li>
            <li onclick="deleteTask(${id})"><i class="fa fa-trash"></i>Delete</li>
        </ul>
    </div>
  </li>`;
    }
  });
  taskBox.innerHTML = li || `<span>not found records</span>`;
  createFilteredOptions(todos);
}

btnClear.addEventListener("click", (e) => {
  todos.splice(0);
  saveTodos(todos);
  showTodos("all");
});

function updateStatus(selectedTask) {
  let taskName = selectedTask.parentElement.lastElementChild;

  if (selectedTask.checked) {
    taskName.classList.add("checked");
    todos[selectedTask.id].status = "completed";
  } else {
    taskName.classList.remove("checked");
    todos[selectedTask.id].status = "pending";
  }

  saveTodos(todos);
}

function showMenu(selectedMenu) {
  let taskMenu = selectedMenu.parentElement.lastElementChild;
  taskMenu.classList.add("show");
  document.addEventListener("click", (e) => {
    if (e.target.tagName != "I" || e.target != selectedMenu) {
      taskMenu.classList.remove("show");
    }
  });
}

function updateTask(id, taskName) {
  isUpdating = true;
  taskInput.value = taskName;
  EditedTaskId = id;
  for (const option of AddCategorySelect.options) {
    if (option.value == todos[id].category) {
      AddCategorySelect.selectedIndex = option.dataset.id;
    }
  }
}

function deleteTask(id) {
  todos = todos.filter((value, index) => index !== id);
  saveTodos(todos);
  showTodos("all");
}

function saveTodos(todos) {
  localStorage.setItem(TODO_LOCAL_KEY, JSON.stringify(todos));
}

showTodos("all");

function createFilteredOptions(todos) {
  if (todos) {
    categories = ["DEFAULT", ...todos.map((task) => task.category).sort()];

    categories = categories.filter((value, index) => {
      if (index !== 0) {
        return value !== categories[index - 1];
      }
      return true;
    });
    createOptions(categories);
  }
}

taskInput.addEventListener("keyup", (e) => {
  let userTask = taskInput.value.trim();
  if (e.key == "Enter" && userTask) {
    category = (
      categoryInput.value ||
      (AddCategorySelect.options[AddCategorySelect.selectedIndex]
        ? AddCategorySelect.options[AddCategorySelect.selectedIndex].value
        : "DEFAULT")
    ).trim();
    if (!isUpdating) {
      let taskInfo = { name: userTask, status: "pending", category: category };
      todos.push(taskInfo);
    } else {
      todos[EditedTaskId].name = userTask;
      todos[EditedTaskId].category = category;
      isUpdating = false;
    }
    if (!categories.find((elt) => elt === category)) {
      categories.push(category);
    }

    saveTodos(todos);
    showTodos("all");
    AddCategorySelect.selectedIndex = 0;
    categoryInput.value = "";
    taskInput.value = "";
  }
});
