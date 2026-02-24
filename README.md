# Task 2: Inventory & Financial Reporting System

This repository contains the solution for **Task 2: Inventory & Financial Reporting**. It is a Laravel-based backend system built for an e-commerce seller panel to manage products, process sales, and easily track daily profit and loss.

## 🌐 Live Demo & Credentials
**Live Application:** [https://inventory-app-pgsl.onrender.com](https://inventory-app-pgsl.onrender.com) 

**Test User Credentials:**
- **Email:** admin@example.com
- **Password:** password

## 📂 Project Overview
The main goal of this project is to keep track of product stock and provide accurate financial reports without complex accounting software. It automatically handles stock deduction, calculates VAT and discounts, and separates cash in hand from due amounts.

## 🚀 Key Features
* **Smart Dashboard:** Gives a quick overview of Today's Sales, Total Inventory Value (based on buying price), and Total Cash Collected.
* **Product & Stock Management:** Sellers can add products with both a `purchase_price` (Cost) and a `sell_price`. Stock automatically decreases when a sale is made.
* **Sales Processing:** When adding a new sale, the system calculates everything perfectly:
  * Applies Discount and adds VAT.
  * Calculates the Net Amount.
  * Tracks how much the customer paid (`paid_amount`) and how much is still due (`due_amount`).
* **Detailed Financial Report:** A dedicated reporting page where users can filter records by date. It shows:
  * Total Sales, Total Collected, and Total Due.
  * Total VAT, Cost of Goods Sold (COGS), and Total Discount.
  * **Net Profit** calculated accurately.

## 🧮 How the Calculations Work (Core Logic)
I kept the logic simple but highly accurate based on standard business math:
1. **Inventory Value:** Calculated by multiplying the available stock by the purchase price of each product.
   `Inventory Value = Stock × Purchase Price`
2. **Cost of Goods Sold (COGS):** When a product is sold, the system calculates how much it originally cost the seller.
   `COGS = Quantity Sold × Purchase Price`
3. **Net Profit:** The final profit is calculated by taking the total sales and subtracting taxes, product costs, and given discounts.
   `Net Profit = (Total Sales - Total VAT) - (COGS + Total Discount)`

