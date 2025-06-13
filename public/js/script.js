// Search functionality
function initializeSearch() {
  const searchInput = document.getElementById("searchInput")
  if (searchInput) {
    searchInput.addEventListener("input", function () {
      const searchTerm = this.value.toLowerCase()
      const recipes = document.querySelectorAll(".recipe-card, .recipe-dashboard-card")

      recipes.forEach((recipe) => {
        const title = recipe.querySelector("h3").textContent.toLowerCase()
        const description = recipe.querySelector("p").textContent.toLowerCase()

        if (title.includes(searchTerm) || description.includes(searchTerm)) {
          recipe.style.display = "block"
        } else {
          recipe.style.display = "none"
        }
      })
    })
  }
}

// Filter functionality
function initializeFilters() {
  const filterButtons = document.querySelectorAll(".filter-btn")
  if (filterButtons.length > 0) {
    filterButtons.forEach((btn) => {
      btn.addEventListener("click", function () {
        // Remove active class from all buttons
        filterButtons.forEach((b) => b.classList.remove("active"))
        // Add active class to clicked button
        this.classList.add("active")

        const filter = this.getAttribute("data-filter")
        const recipes = document.querySelectorAll(".recipe-card, .recipe-dashboard-card")

        recipes.forEach((recipe) => {
          if (filter === "all" || recipe.getAttribute("data-difficulty") === filter) {
            recipe.style.display = "block"
          } else {
            recipe.style.display = "none"
          }
        })
      })
    })
  }
}

// Sidebar filters functionality
function initializeSidebarFilters() {
  const applyFiltersBtn = document.querySelector(".btn-filter")
  if (applyFiltersBtn) {
    applyFiltersBtn.addEventListener("click", () => {
      const selectedCuisines = []
      const selectedIngredients = []
      const selectedDifficulties = []

      // Get selected filters
      document.querySelectorAll(".filter-section").forEach((section) => {
        const sectionTitle = section.querySelector("h4").textContent.toLowerCase()
        const checkedBoxes = section.querySelectorAll('input[type="checkbox"]:checked')

        checkedBoxes.forEach((checkbox) => {
          const value = checkbox.value
          if (sectionTitle.includes("cuisine")) {
            selectedCuisines.push(value)
          } else if (sectionTitle.includes("ingredients")) {
            selectedIngredients.push(value)
          } else if (sectionTitle.includes("difficulty")) {
            selectedDifficulties.push(value)
          }
        })
      })

      // Apply filters to recipes
      const recipes = document.querySelectorAll(".recipe-card")
      let visibleCount = 0

      recipes.forEach((recipe) => {
        let showRecipe = true

        // Check difficulty filter
        if (selectedDifficulties.length > 0) {
          const recipeDifficulty = recipe.getAttribute("data-difficulty")
          if (!selectedDifficulties.includes(recipeDifficulty)) {
            showRecipe = false
          }
        }

        // Check cuisine filter
        if (selectedCuisines.length > 0) {
          const recipeCuisine = recipe.getAttribute("data-cuisine")
          if (!selectedCuisines.includes(recipeCuisine)) {
            showRecipe = false
          }
        }

        // Check ingredients filter (search in recipe content)
        if (selectedIngredients.length > 0) {
          const recipeText = recipe.textContent.toLowerCase()
          const hasIngredient = selectedIngredients.some((ingredient) => recipeText.includes(ingredient.toLowerCase()))
          if (!hasIngredient) {
            showRecipe = false
          }
        }

        if (showRecipe) {
          recipe.style.display = "block"
          visibleCount++
        } else {
          recipe.style.display = "none"
        }
      })

      // Show message if no recipes match
      showFilterResults(visibleCount)
    })
  }

  // Reset filters button
  const resetBtn = document.createElement("button")
  resetBtn.textContent = "Reset Filters"
  resetBtn.className = "btn-filter"
  resetBtn.style.background = "#6c757d"
  resetBtn.style.marginTop = "0.5rem"

  resetBtn.addEventListener("click", () => {
    // Uncheck all checkboxes
    document.querySelectorAll('.filter-section input[type="checkbox"]').forEach((checkbox) => {
      checkbox.checked = false
    })

    // Show all recipes
    document.querySelectorAll(".recipe-card").forEach((recipe) => {
      recipe.style.display = "block"
    })

    // Remove any filter messages
    const filterMessage = document.querySelector(".filter-message")
    if (filterMessage) {
      filterMessage.remove()
    }
  })

  // Add reset button after apply button
  const sidebar = document.querySelector(".sidebar")
  if (sidebar && applyFiltersBtn) {
    applyFiltersBtn.parentNode.appendChild(resetBtn)
  }
}

function showFilterResults(count) {
  // Remove existing message
  const existingMessage = document.querySelector(".filter-message")
  if (existingMessage) {
    existingMessage.remove()
  }

  // Add new message
  const recipesGrid = document.querySelector(".recipes-grid")
  if (recipesGrid) {
    const message = document.createElement("div")
    message.className = "filter-message"
    message.style.gridColumn = "1 / -1"
    message.style.textAlign = "center"
    message.style.padding = "2rem"
    message.style.color = "#666"

    if (count === 0) {
      message.innerHTML = `
        <i class="fas fa-filter fa-2x" style="margin-bottom: 1rem; color: #ddd;"></i>
        <h3>No recipes match your filters</h3>
        <p>Try adjusting your filter criteria</p>
      `
    } else {
      message.innerHTML = `
        <p style="background: #e8f5e8; color: #2e7d32; padding: 1rem; border-radius: 6px; margin: 0;">
          <i class="fas fa-check-circle"></i> Found ${count} recipe${count !== 1 ? "s" : ""} matching your filters
        </p>
      `
    }

    recipesGrid.appendChild(message)

    // Auto-remove success message after 3 seconds
    if (count > 0) {
      setTimeout(() => {
        if (message.parentNode) {
          message.remove()
        }
      }, 3000)
    }
  }
}

// Real-time filter for checkboxes
function initializeRealtimeFilters() {
  document.querySelectorAll('.filter-section input[type="checkbox"]').forEach((checkbox) => {
    checkbox.addEventListener("change", () => {
      // Auto-apply filters when checkbox changes
      const applyBtn = document.querySelector(".btn-filter")
      if (applyBtn) {
        applyBtn.click()
      }
    })
  })
}

// Mobile navigation toggle
function initializeMobileNav() {
  const navToggle = document.createElement("button")
  navToggle.className = "nav-toggle"
  navToggle.innerHTML = '<i class="fas fa-bars"></i>'
  navToggle.style.display = "none"

  const navContainer = document.querySelector(".nav-container")
  const navMenu = document.querySelector(".nav-menu")

  if (navContainer && navMenu) {
    navContainer.insertBefore(navToggle, navMenu)

    // Show toggle button on mobile
    function checkMobile() {
      if (window.innerWidth <= 768) {
        navToggle.style.display = "block"
        navMenu.style.display = "none"
      } else {
        navToggle.style.display = "none"
        navMenu.style.display = "flex"
        navMenu.classList.remove("nav-menu-active")
      }
    }

    navToggle.addEventListener("click", () => {
      if (navMenu.style.display === "none" || !navMenu.style.display) {
        navMenu.style.display = "flex"
        navMenu.style.flexDirection = "column"
        navMenu.style.position = "absolute"
        navMenu.style.top = "100%"
        navMenu.style.right = "0"
        navMenu.style.background = "white"
        navMenu.style.boxShadow = "0 4px 6px rgba(0,0,0,0.1)"
        navMenu.style.padding = "1rem"
        navMenu.style.borderRadius = "6px"
        navMenu.style.minWidth = "200px"
        navMenu.style.zIndex = "1000"
      } else {
        navMenu.style.display = "none"
      }
    })

    window.addEventListener("resize", checkMobile)
    checkMobile()
  }
}

// Smooth scrolling for anchor links
function initializeSmoothScroll() {
  document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
    anchor.addEventListener("click", function (e) {
      e.preventDefault()
      const target = document.querySelector(this.getAttribute("href"))
      if (target) {
        target.scrollIntoView({
          behavior: "smooth",
        })
      }
    })
  })
}

// Form validation
function initializeFormValidation() {
  const forms = document.querySelectorAll("form")
  forms.forEach((form) => {
    form.addEventListener("submit", (e) => {
      const requiredFields = form.querySelectorAll("[required]")
      let isValid = true

      requiredFields.forEach((field) => {
        if (!field.value.trim()) {
          field.classList.add("error")
          field.style.borderColor = "#dc3545"
          isValid = false
        } else {
          field.classList.remove("error")
          field.style.borderColor = "#ddd"
        }
      })

      if (!isValid) {
        e.preventDefault()
        alert("Please fill in all required fields.")
      }
    })
  })
}

// Initialize all functionality when DOM is loaded
document.addEventListener("DOMContentLoaded", () => {
  initializeSearch()
  initializeFilters()
  initializeSidebarFilters()
  initializeRealtimeFilters()
  initializeMobileNav()
  initializeSmoothScroll()
  initializeFormValidation()
})

// Recipe interaction functions
function toggleSave(recipeId) {
  fetch(`../actions/toggle_save.php?id=${recipeId}`, {
    method: "POST",
    headers: {
      "X-Requested-With": "XMLHttpRequest",
    },
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        // Find and update all save buttons for this recipe
        const saveButtons = document.querySelectorAll(`[onclick="toggleSave(${recipeId})"]`)
        saveButtons.forEach((btn) => {
          const icon = btn.querySelector("i")
          if (icon) {
            icon.classList.toggle("far")
            icon.classList.toggle("fas")
          }

          // Update button text if it exists
          if (btn.textContent.includes("Save")) {
            btn.innerHTML = btn.innerHTML.includes("Unsave")
              ? '<i class="far fa-heart"></i> Save'
              : '<i class="fas fa-heart"></i> Saved'
          }
        })
      }
    })
    .catch((error) => {
      console.error("Error:", error)
    })
}

function searchRecipes() {
  const searchInput = document.getElementById("searchInput")
  if (searchInput) {
    const searchTerm = searchInput.value.toLowerCase()
    const recipes = document.querySelectorAll(".recipe-card, .recipe-dashboard-card")

    recipes.forEach((recipe) => {
      const title = recipe.querySelector("h3").textContent.toLowerCase()
      const description = recipe.querySelector("p").textContent.toLowerCase()

      if (title.includes(searchTerm) || description.includes(searchTerm)) {
        recipe.style.display = "block"
      } else {
        recipe.style.display = "none"
      }
    })
  }
}

// Global search function for homepage
function performGlobalSearch() {
  const searchInput = document.querySelector(".search-input")
  if (searchInput) {
    const searchTerm = searchInput.value.toLowerCase()

    if (searchTerm.trim() === "") {
      // Show all recipes if search is empty
      document.querySelectorAll(".recipe-card").forEach((recipe) => {
        recipe.style.display = "block"
      })
      return
    }

    const recipes = document.querySelectorAll(".recipe-card")
    let hasResults = false

    recipes.forEach((recipe) => {
      const title = recipe.querySelector("h3").textContent.toLowerCase()
      const description = recipe.querySelector("p").textContent.toLowerCase()

      if (title.includes(searchTerm) || description.includes(searchTerm)) {
        recipe.style.display = "block"
        hasResults = true
      } else {
        recipe.style.display = "none"
      }
    })

    // Show no results message if needed
    if (!hasResults) {
      const recipesGrid = document.querySelector(".recipes-grid")
      if (recipesGrid && !document.querySelector(".no-results")) {
        const noResults = document.createElement("div")
        noResults.className = "no-results empty-state"
        noResults.innerHTML = `
          <i class="fas fa-search fa-3x"></i>
          <h3>No recipes found</h3>
          <p>Try searching with different keywords</p>
        `
        recipesGrid.appendChild(noResults)
      }
    } else {
      // Remove no results message if it exists
      const noResults = document.querySelector(".no-results")
      if (noResults) {
        noResults.remove()
      }
    }
  }
}

// Add event listeners for search
document.addEventListener("DOMContentLoaded", () => {
  const searchBtn = document.querySelector(".search-btn")
  const searchInput = document.querySelector(".search-input")

  if (searchBtn) {
    searchBtn.addEventListener("click", performGlobalSearch)
  }

  if (searchInput) {
    searchInput.addEventListener("keypress", (e) => {
      if (e.key === "Enter") {
        performGlobalSearch()
      }
    })

    // Real-time search
    searchInput.addEventListener("input", performGlobalSearch)
  }
})
