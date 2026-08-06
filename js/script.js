// Item data
let items = [
  {
    id: 1,
    name: "Black Backpack",
    type: "lost",
    category: "Bags",
    color: "Black",
    location: "Central Library",
    date: "2026-07-22",
    description: "Black backpack with notebooks and a calculator.",
    icon: "🎒"
  },
  {
    id: 2,
    name: "ID Card",
    type: "found",
    category: "Documents",
    color: "White",
    location: "Main Cafeteria",
    date: "2026-07-23",
    description: "ID card found near the payment counter.",
    icon: "🪪"
  },
  {
    id: 3,
    name: "Calculator",
    type: "lost",
    category: "Electronics",
    color: "Gray",
    location: "Computer Lab",
    date: "2026-07-20",
    description: "Scientific calculator with initials on the back.",
    icon: "🧮"
  },
  {
    id: 4,
    name: "Blue Notebook",
    type: "found",
    category: "Books",
    color: "Blue",
    location: "Meeting Room",
    date: "2026-07-21",
    description: "Blue notebook with handwritten notes.",
    icon: "📘"
  },
  {
    id: 5,
    name: "Silver Keys",
    type: "lost",
    category: "Keys",
    color: "Silver",
    location: "Public Park",
    date: "2026-07-19",
    description: "Three keys with a red key ring.",
    icon: "🔑"
  },
  {
    id: 6,
    name: "Brown Wallet",
    type: "found",
    category: "Accessories",
    color: "Brown",
    location: "Parking Area",
    date: "2026-07-18",
    description: "Brown wallet found near the entrance.",
    icon: "👛"
  }
];

// Create one item card
function createCard(item) {
  return `
    <div class="card">
      <div class="image">${item.icon}</div>

      <div class="info">
        <div class="cardTop">
          <span class="type ${item.type}">${item.type.toUpperCase()}</span>
          <span class="date">${item.date}</span>
        </div>

        <h3>${item.name}</h3>
        <p><b>Category:</b> ${item.category}</p>
        <p><b>Location:</b> ${item.location}</p>

        <button class="cardBtn" onclick="showDetails(${item.id})">
          View Details
        </button>
      </div>
    </div>
  `;
}

// Show recent items on home page
function showHomeItems() {
  let homeItems = document.getElementById("homeItems");

  if (homeItems) {
    homeItems.innerHTML = "";

    for (let i = 0; i < 3; i++) {
      homeItems.innerHTML += createCard(items[i]);
    }
  }
}

// Show all items on browse page
function showItems(list) {
  let allItems = document.getElementById("allItems");
  let resultCount = document.getElementById("resultCount");
  let noItem = document.getElementById("noItem");

  if (!allItems) {
    return;
  }

  allItems.innerHTML = "";

  for (let i = 0; i < list.length; i++) {
    allItems.innerHTML += createCard(list[i]);
  }

  resultCount.innerText = list.length;

  if (list.length === 0) {
    noItem.style.display = "block";
  } else {
    noItem.style.display = "none";
  }
}

// Search and filter items
function searchItems() {
  let searchInput = document.getElementById("searchInput");
  let typeFilter = document.getElementById("typeFilter");
  let categoryFilter = document.getElementById("categoryFilter");

  if (!searchInput) {
    return;
  }

  let searchText = searchInput.value.toLowerCase();
  let type = typeFilter.value;
  let category = categoryFilter.value;
  let result = [];

  for (let i = 0; i < items.length; i++) {
    let itemName = items[i].name.toLowerCase();
    let itemLocation = items[i].location.toLowerCase();

    let matchText =
      itemName.includes(searchText) ||
      itemLocation.includes(searchText);

    let matchType =
      type === "all" ||
      items[i].type === type;

    let matchCategory =
      category === "all" ||
      items[i].category === category;

    if (matchText && matchType && matchCategory) {
      result.push(items[i]);
    }
  }

  showItems(result);
}