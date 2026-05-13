# Zoho CRM Lead Test Curl

Use this PowerShell example to test the same request shape the application now sends to Zoho CRM.

## Endpoint

`POST https://www.zohoapis.com/crm/v8/Leads`

## PowerShell curl.exe example

```powershell
$body = @'
{
  "data": [
    {
      "Owner": {
        "name": "Alex Ndhlovu",
        "id": "2960730000120595001",
        "email": "salcorporate@infinitybrands.co.za"
      },
      "First_Name": "Robin",
      "Last_Name": "Myndos",
      "Email": "info@myndos.co.za",
      "Mobile": "0763029206",
      "Phone": "0763029206",
      "Company": "Myndos",
      "Lead_Stage": "1. New (Important)",
      "Lead_Status": "New",
      "Interest": "INFX : ZOHO (Implementation)",
      "Brand": "INFX: Zoho",
      "Lead_Source": "INFX Zoho Magnet",
      "Franchise": "INFX Solutions",
      "Product": "Corporate (COR)",
      "Sales_Department": "SAL COR: Product Sales",
      "Tag": [
        { "name": "INFXS" },
        { "name": "INFX" }
      ],
      "Description": "Product: Zoho One\n\nLead magnet answers\n1. What do you need to improve? Run the business from one connected set of tools\n2. What is slowing you down? Too much manual work between teams\n3. What does your team look like? A growing team across several departments\n4. When do you want this live? This quarter\n5. Where should we send it? Robin Myndos / info@myndos.co.za / 0763029206 / Myndos\n\nProduct page answers\n1. How many people will use this? 11-50 people\n2. When would you like to get started? This quarter\n3. What does the current setup look like? Too many separate business tools are in use\n4. What is the main result you want from this product? One connected setup across the business"
    }
  ]
}
'@

curl.exe --request POST "https://www.zohoapis.com/crm/v8/Leads" `
  --header "Authorization: Zoho-oauthtoken YOUR_ACCESS_TOKEN" `
  --header "Content-Type: application/json" `
  --data-raw $body
```

## JSON payload only

```json
{
  "data": [
    {
      "Owner": {
        "name": "Alex Ndhlovu",
        "id": "2960730000120595001",
        "email": "salcorporate@infinitybrands.co.za"
      },
      "First_Name": "Robin",
      "Last_Name": "Myndos",
      "Email": "info@myndos.co.za",
      "Mobile": "0763029206",
      "Phone": "0763029206",
      "Company": "Myndos",
      "Lead_Stage": "1. New (Important)",
      "Lead_Status": "New",
      "Interest": "INFX : ZOHO (Implementation)",
      "Brand": "INFX: Zoho",
      "Lead_Source": "INFX Zoho Magnet",
      "Franchise": "INFX Solutions",
      "Product": "Corporate (COR)",
      "Sales_Department": "SAL COR: Product Sales",
      "Tag": [
        { "name": "INFXS" },
        { "name": "INFX" }
      ],
      "Description": "Product: Zoho One\n\nLead magnet answers\n1. What do you need to improve? Run the business from one connected set of tools\n2. What is slowing you down? Too much manual work between teams\n3. What does your team look like? A growing team across several departments\n4. When do you want this live? This quarter\n5. Where should we send it? Robin Myndos / info@myndos.co.za / 0763029206 / Myndos\n\nProduct page answers\n1. How many people will use this? 11-50 people\n2. When would you like to get started? This quarter\n3. What does the current setup look like? Too many separate business tools are in use\n4. What is the main result you want from this product? One connected setup across the business"
    }
  ]
}
```
