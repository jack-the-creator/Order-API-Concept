# Orders API Project
Extras I would've liked to add:
- User authentication
- Create DeliveryAddress Entity that holds Street Name 1, Street Name 2, City, Postcode. Allow a user to have multiple addresses and have one per order
- Create Items Entity that holds a Name, Price, Stock
- Allow an Order to have a collection of OrderItems (Item, Quantity)
- If there is not enough Item Stock, do not allow Order creation
- Add Total Cost to Order, adding up the price of each Item Price * Quantity

## Create Order
**POST** /api/orders

Creates a new Order and sets the estimated delivery date depending on the delivery option.

Request payload:
- name
- deliveryAddress
- deliveryOption
- orderItems (id, quantity)

Accepted deliveryOption types: `[ "next_day", "next_day_by_midday", "standard", "yesterday" ]`

Example:
```
{
    "name": "Jack Arnold",
    "deliveryAddress": "5 Coolville",
    "deliveryOption": "next_day",
    "orderItems": {
        "id": 1,
        "quantity": 5
    }
}
```
Expected return:
```
{
    "message": "New Order created (1)",
    "order": {
        "id": 1,
        "name": "Jack Arnold",
        "deliveryAddress": "5 Coolville",
        "deliveryOption": "next_day",
        "status": "pending",
        "estimatedDeliveryDate": "2023-07-20T22:00:00+00:00",
        "OrderItems": {
            "id": 1,
            "quantity": 5
        }
    }
}
```

## Get Order(s)
**GET** /api/orders

Gets Order(s) by Order Id or Status - if no parameter is passed, returns all orders.
**Note:** Order Id will be prioritised over Status if both are sent.

URL Params:
- id
- status

Accepted status types: `[ "pending", "processing", "out_for_delivery", "delivered", "delayed" ]`

Example:
```
/api/orders?id=1
/api/orders?status=pending
```
Return:
```
{
    "id": 1,
    "name": "Jack Arnold",
    "deliveryAddress": "5 Coolville",
    "deliveryOption": "next_day",
    "status": "pending",
    "estimatedDeliveryDate": "2023-07-20T22:00:00+00:00",
    "OrderItems": {
        "id": 1,
        "quantity": 5
    }
}
```

## Update Order
**PATCH** /api/orders

Finds an Order by Id and updates the Status that is sent through the request.

Request payload:
- id
- status

Accepted status types: `[ "pending", "processing", "out_for_delivery", "delivered", "delayed" ]`

Example:
```
{
    "id": 1,
    "status": "out_for_delivery"
}
```
Expected return:
```
{
    "message": "Order (1) delivery status updated to 'out_for_delivery'",
    "order": {
        "id": 1,
        "name": "Jack Arnold",
        "deliveryAddress": "5 Coolville",
        "deliveryOption": "next_day",
        "status": "out_for_delivery",
        "estimatedDeliveryDate": "2023-07-20T22:00:00+00:00",
        "OrderItems": {
            "id": 1,
            "quantity": 5
        }
    }
}
```

## Delay Orders Command
This command delays all orders with estimated delivery dates before the time you execute this command.
```
php bin/console app:delay-orders
```
