# hubspot-to-saleslink

### WP Plugin for Hubspot to SalesLink Integration

__Test Environment:__ [https://hspot2slink.wpengine.com](https://hspot2slink.wpengine.com/)

[![Deployment status from DeployBot](https://ssi.deploybot.com/badge/23779030075075/202909.svg)](https://deploybot.com)

### Service Accounts

- __Hubspot Account:__ [Production](https://app.hubspot.com/workflows/21230240/platform/flow/202159288/edit)
- __SalesLink Account:__ [Sandbox](https://use.sandbox-cloudlink.uptake.com/hawthorne/myapplications/) | [Production](https://use.cloudlink.uptake.com/hawthorne)

## Flow Overview

1. A Hubspot user updates a Deal record such that a workflow triggers a webhook action to a "secure" endpoint provided by the plugin.
2. The Hubspot Deal data is then mapped to a set for attributes for an Opportunity in SalesLink.
3. The new Opportunity record is pushed via the SalesLink API (stage is set to "Lead").
4. A cron job checks every 5 minutes for any updates to the SalesLink Opportunity record.
5. Any found updates are pushed back to Hubspot via the Hubspot API.


## Configuration

These values can be set in wp-config.php or as environment variables.

- `HUBSPOT_TO_SALESLINK_APP_ID`: Used to initialize API calls to the HubSpot API.
- `HUBSPOT_TO_SALESLINK_WEBHOOK_SECRET`: Shared secret that is set here and set as the `key` URL param for the webhook endpoint inside of the HubSpot workflow.
- `HUBSPOT_TO_SALESLINK_LOG_PATH`: Path to logs from the plugin.
- `HUBSPOT_TO_SALESLINK_APP_CLIENT_SECRET`: Optional - The HubSpot App Client Secret needed for WebHook validation if using the v2 token method. This valid should come from a public app (private apps don't expose the Client Secret value).
  Note: By default, we use a shared secret validation method so that a public app is not needed.
- `HUBSPOT_TO_SALESLINK_HUBSPOT_OAUTH_TOKEN`: The token for the Private HubSpot app used to interact with the HubSpot API
- `HUBSPOT_TO_SALESLINK_CLOUDLINK_API_BASE_URI`: The base URI for the SalesLink API (changes depending on environment)
- `HUBSPOT_TO_SALESLINK_CLOUDLINK_API_KEY`: The API Key for authenticating to the SalesLink API (changes depending on environment)


## Hubspot Webhook

The "Deal to SalesLink" Workflow has the following Deal enrollment triggers:

- Create date is known _AND_
- Pipeline is any of Sales Pipeline _AND_
- Deal stage is any of Sales Qualified Lead (Sales Pipeline) _AND_
- Last synced from SalesLink is unknown

The workflow's webhook POST endpoint: https://hspot2slink.wpengine.com/wp-json/hs2sl/v1/deal?key=<HUBSPOT_TO_SALESLINK_WEBHOOK_SECRET>

The webhook validation method for this initial build uses a shared secret, which is passed via a URL param in the endpoint (see: `HUBSPOT_TO_SALESLINK_WEBHOOK_SECRET`). There is also validation in place for future-use as a public Hubspot App instead of a share secret, if/when needed.

For webhook functionality, the Hubspot account must have the Operations Hub / Pro plan.


## Logging

The logs are currently configured to be written to the plugin's `logs` sub-directory. This path can be updated via `HUBSPOT_TO_SALESLINK_LOG_PATH`.


## Testing

### To trigger a test from Hubspot...

1. Visit the workflow page in HubSpot (https://app.hubspot.com/workflows/21230240/platform/flow/202159288/edit/actions/1/webhook).
2. Click on the "Send" a webhook action.
3. In the right menu, open "Test Action", then select a test Deal (one is available already), then click the "Test" button.
4. The response should be a `200`.

To verify that it is working, you should see the following things logged:

- The request headers and body
- The result of validation
- If validated, a sample message showing the Deal ID

### To manually trigger a test of the cron job watching SalesLink...

There is a [WP-Cron](https://developer.wordpress.org/plugins/cron/) checking for changes in SalesLink every 5 minutes. WP-Cron will only check to see if a cron task is scheduled to run on page load.

1. With the [WP Crontrol](https://wordpress.org/plugins/wp-crontrol/) plugin installed, visit https://hspot2slink.wpengine.com/wp-admin/tools.php?page=crontrol_admin_manage_page and manually trigger the `hs2sl_cron_hook`. 
2. Alternatively, make a request to `/wp-cron.php` to trigger them on-demand, and that request can be executed by the scheduler of choice.

Leads should be visible in the SalesLink Sandbox [here](https://use.sandbox-cloudlink.uptake.com/hawthorne/SalesLink/Executive/modules/opportunity/reports/rpt_OppList_CreateDate.aspx?tabIdx=Source&monthId=0&yearId=0&ViewPoint=1&datefiltertype=2&createyear=2022&DivisionList=%25&RegionIdList=0&SourceGroupIdList=0&SourceIdList=0&CampaignIdList=0&SourceOriginIdList=0&ContactTypeIdList=0&BaseModelIdList=%25&blnOnlyWinningBidders=0&SalesRepIdList=0&BranchNoList=%25&FamilyIdList=0&DateSearchId=1&IndustryGroupIdList=0&IndustryCodeIdList=%25&OppEquipStatus=1%7C@%5e1%7CN%5e1%7CU%5e2%7C@%5e2%7CN%5e2%7CU&ClassificationIdList=0&probabilityOfClosing=7&quoteStatus=1&opportunityType=15&BidOppId=3).

