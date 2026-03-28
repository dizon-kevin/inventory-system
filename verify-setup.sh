#!/bin/bash

# Inventory System Setup Verification Script
# This script checks if all components are properly configured

echo "🔍 Checking Notification System Setup..."
echo ""

# Color codes
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Function to check file existence
check_file() {
    if [ -f "$1" ]; then
        echo -e "${GREEN}✓${NC} $1"
        return 0
    else
        echo -e "${RED}✗${NC} $1 (MISSING)"
        return 1
    fi
}

# Function to check directory existence
check_dir() {
    if [ -d "$1" ]; then
        echo -e "${GREEN}✓${NC} $1"
        return 0
    else
        echo -e "${RED}✗${NC} $1 (MISSING)"
        return 1
    fi
}

# Function to check file contains content
check_content() {
    if grep -q "$2" "$1" 2>/dev/null; then
        echo -e "${GREEN}✓${NC} $1 contains '$2'"
        return 0
    else
        echo -e "${YELLOW}⚠${NC} $1 missing '$2'"
        return 1
    fi
}

echo "📁 Checking Configuration Files..."
check_file "config/broadcasting.php"
check_file ".env"

echo ""
echo "📁 Checking Application Files..."
check_file "app/Events/NotificationCreated.php"
check_file "app/Listeners/SendNotification.php"
check_file "app/Models/Notification.php"
check_file "app/Http/Controllers/UserDashboardController.php"

echo ""
echo "📁 Checking View Files..."
check_file "resources/views/layouts/navigation.blade.php"
check_file "resources/views/admin/dashboard.blade.php"
check_file "resources/views/user/dashboard.blade.php"

echo ""
echo "📁 Checking JavaScript Files..."
check_file "resources/js/bootstrap-echo.js"
check_file "resources/js/app.js"

echo ""
echo "📋 Checking Content..."
check_content "resources/js/app.js" "bootstrap-echo"
check_content "resources/views/layouts/navigation.blade.php" "data-user-id"
check_content "app/Listeners/SendNotification.php" "NotificationCreated"

echo ""
echo "📝 Checking Environment Setup..."
if grep -q "BROADCAST_DRIVER" .env; then
    BROADCAST_DRIVER=$(grep "BROADCAST_DRIVER" .env | cut -d'=' -f2)
    echo -e "${GREEN}✓${NC} BROADCAST_DRIVER=$BROADCAST_DRIVER"
else
    echo -e "${RED}✗${NC} BROADCAST_DRIVER not set in .env"
fi

if grep -q "PUSHER_APP_KEY" .env; then
    echo -e "${GREEN}✓${NC} Pusher credentials found"
else
    echo -e "${YELLOW}⚠${NC} Pusher credentials not fully configured"
fi

echo ""
echo "🔧 Database Check..."
if command -v php &> /dev/null; then
    echo "Checking if migrations have been run..."
    # We can't easily check without database connection, so just note it
    echo -e "${YELLOW}⚠${NC} Please verify database migrations with: php artisan migrate:status"
else
    echo -e "${RED}✗${NC} PHP not found in PATH"
fi

echo ""
echo "✅ Setup Verification Complete!"
echo ""
echo "📋 Next Steps:"
echo "1. Verify BROADCAST_DRIVER is not 'null' in .env"
echo "2. Configure Pusher credentials or WebSockets"
echo "3. Run: npm run build"
echo "4. Run: php artisan migrate (if not already done)"
echo "5. Test the system by creating a notification"
