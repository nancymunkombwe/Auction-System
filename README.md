# Auctify
Blue PHP/MySQL auction marketplace. Discover. Bid. Win.

## Run locally (XAMPP)
Keep the existing `auction_system` database and place this folder in `htdocs/auction-system`. Start Apache and MySQL, then open http://localhost/auction-system/. The existing database was not included in the original archive. The code expects users(id, username, email, password), auctions(id, title, description, starting_price, current_price, end_time, image), and bids(auction_id, user_id, amount, created_at). Use InnoDB for transaction locking. Back up your database before changes. DB_HOST, DB_USER, DB_PASSWORD, DB_NAME can override local defaults. Auction times are interpreted in Africa/Lusaka. Prices are displayed in ZMW; existing numeric values are not converted.

## Publish changes
Copy the files over the existing Git checkout, retaining its .git directory and any local uploads/config. Run `git status`, `git add .`, `git commit -m "Redesign Auctify marketplace"`, `git push origin main`. PHP/MySQL require a compatible host; GitHub Pages alone cannot run this project.

PHP syntax checked. No live database supplied; end-to-end functionality must be tested locally. No payment integration, seller ownership, or real-time socket updates yet.
