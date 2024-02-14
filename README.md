# troubleshooting programmatic orders and invoices
Temporary Repo for random needs. 

This repository analyzes:
Order: 5k7wpfiroI7QRRK9qHQ4a6VjhQcZY
Invoice: inv:0-ChALdp-Kr2g9zqoKMTCuTLtEEO8J

originalOrder.txt represents the order object after its creation using the programmatic Square API, but before any edits have been made. 
OriginalInvoice.txt represents the invoice object that was created programmatically, but before any edits have been made. 

OrderAfterInvoice.txt represents the same order object, AFTER the INVOICE was edited on the square website. 
InvoiceAfteEditing.txt represents the same invoice object, AFTER it was edited on the square website. 

The differences between the "before" and "after" invoices are minor and do not suggest any major issues. 
The differences between the "before" and "after" orders suggest more is going on, so I summarized below: 

**Total Tax Money and Applied Taxes:
**
In the first order JSON, the total tax money for the line item is $189 USD, and the overall order's total tax money is $204 USD. The applied tax UID for the line item is "NXE76qbwyIjDztnq82NOZB", indicating a specific tax applied.
In the second JSON, the total tax money for the line item is reduced to $188 USD, and the overall order's total tax money matches this reduction, also at $188 USD. The applied tax UID for the line item has been changed to "StfARwgQKz3ShuX88y9P0", showing a different tax application or a correction.

**Total Money:
**
The total money for the line item in the first JSON is $4189 USD, while in the second JSON, it's slightly lower at $4188 USD due to the adjusted tax amount.
The total money for the entire order also reflects this change, with the first JSON showing $4524 USD and the second showing a reduced amount of $4508 USD.

**Service Charges:
**
In both JSONs, the service charge is listed with an amount of $320 USD. However, the total tax money applied to the service charge in the first JSON is $15 USD, indicating that tax was applied to the service fee. In the second JSON, the total tax money on the service charge is $0 USD, suggesting a change in the tax treatment of the service charge.

**Order Version and Updated At:
**
The version of the order in the first JSON is 3, and the updated_at timestamp is "2024-02-14T01:27:27.080Z". This indicates the state of the order at that version and time.
In the second JSON, the version is incremented to 4, and the updated_at timestamp is updated to "2024-02-14T01:31:32.242Z", showing that the order was modified or updated after the first JSON snapshot was taken.

**UIDs for Applied Taxes:
**
There's a change in the UID used for applied taxes on the line item, indicating a possible correction or update in the tax application details.
These differences suggest that the second JSON represents an updated version of the order, with adjustments made to the tax calculations, the treatment of service charges, and the total amounts due. The changes likely reflect a correction or an update to the order's financial details, possibly after a review or additional processing.
