<?php
/**
 * @package hubspot-to-saleslink
 */

namespace Snapshot\HubspotToSaleslink\Core\Integrations\SalesLink;

use Carbon\Carbon;
use Snapshot\HubspotToSaleslink\Core\Utils\Countries;

/**
 * Utility methods for interacting with SalesLink
 */
class Utils
{
    /**
     * Factory method to create a SalesLink Opportunity from a HubSpot Deal and related data
     *
     * @param  mixed $hubSpotDeal
     * @return object
     */
    public static function hubSpotDealToSalesLinkOpportunity(object $hubSpotDeal)
    {
        $opp = new \stdClass();

        // Customer Fields
        $opp->customerId = static::getHubSpotObjectProperty($hubSpotDeal->data, 'customer_id');
        if (substr($opp->customerId, 0, 1) !== '$') {
            $opp->customerId = '$' . $opp->customerId;
        }

        $opp->customerName = static::getHubSpotObjectProperty($hubSpotDeal->company, 'name');
        $opp->customerAddress = static::getHubSpotObjectProperty($hubSpotDeal->company, 'address');
        $opp->customerCity = static::getHubSpotObjectProperty($hubSpotDeal->company, 'city');
        $opp->customerZipCode = static::getHubSpotObjectProperty($hubSpotDeal->company, 'zip');
        $opp->customerState = static::getHubSpotObjectProperty($hubSpotDeal->company, 'state');
        $opp->customerCountry = static::formatCountry(static::getHubSpotObjectProperty($hubSpotDeal->company, 'country'));

        // Contact Fields
        $contactNames = [];
        $contactFirstName = static::getHubSpotObjectProperty($hubSpotDeal->contact, 'firstname');
        if ($contactFirstName) {
            array_push($contactNames, $contactFirstName);
        }
        $contactLastName = static::getHubSpotObjectProperty($hubSpotDeal->contact, 'lastname');
        if ($contactLastName) {
            array_push($contactNames, $contactLastName);
        }
        $opp->contactName = implode(" ", $contactNames);
        $opp->contactPhone = static::getHubSpotObjectProperty($hubSpotDeal->contact, 'phone');
        $opp->contactEmail = static::getHubSpotObjectProperty($hubSpotDeal->contact, 'email');

        // Other
        $opp->description = static::getHubSpotObjectProperty($hubSpotDeal->data, 'dealname');
        $opp->divisionId = static::getHubSpotObjectProperty($hubSpotDeal->data, 'division_id');
        $opp->typeId = 1; // Sales

        // Not sure if this is the best field to drive with
        // 1 = Low, 2 = Medium, 4 = High - located via CloudLink API
        $priority = static::getHubSpotObjectProperty($hubSpotDeal->data, 'hs_priority');
        switch ($priority) {
            case "medium":
                $probabilityOfClosingId = 2;
                break;
            case "high":
                $probabilityOfClosingId = 4;
                break;
            default:
                $probabilityOfClosingId = 1;
        }
        $opp->probabilityOfClosingId = $probabilityOfClosingId;

        $closeDate = Carbon::parse(
            static::getHubSpotObjectProperty($hubSpotDeal->data, 'closedate') / 1000
        )->subMinute();
        $opp->estimateDeliveryYear = $closeDate->year;
        $opp->estimateDeliveryMonth = $closeDate->month;
        $opp->stageId = 1; // Lead
        $opp->isUrgent = static::getHubSpotObjectProperty($hubSpotDeal->data, 'hs_priority') == "high";
        $opp->ownerUserId = intval(static::getHubSpotObjectProperty($hubSpotDeal->data, 'owner_user_id'));
        // $opp->originatorUserId = 0; // Skipping per Snapshot
        $opp->sourceId = 13; // Lead Alert - determined from API
        // $opp->campaignId = 0; // Skipping per Snapshot
        $opp->externalReferenceNumber = $hubSpotDeal->data->objectId;
        $opp->branchId = static::getHubSpotObjectProperty($hubSpotDeal->data, 'branch_id');

        // Products
        $rawProductsStr = static::getHubSpotObjectProperty($hubSpotDeal->data, 'product_groups');

        if ($rawProductsStr) {
            $rawProducts = preg_split("/\r\n|\n|\r/", $rawProductsStr);
            $group = new \stdClass();
            $group->products = array_map(function ($rawProduct) {
                $product = new \stdClass();
                $product->description = $rawProduct;

                return $product;
            }, $rawProducts);

            // At least one must be marked as primary
            if (isset($group->products[0])) {
                $group->products[0]->isPrimary = true;
            }

            $opp->productGroups = [$group];
        }

        // Notes
        $opp->notes = array_map(function ($note) {
            $slNote = new \stdClass();
            $slNote->text = $note->metadata->body;

            return $slNote;
        }, $hubSpotDeal->notes ?? []);

        return $opp;
    }

    /**
     * Factory method to update a HubSpot Deal and related data from a SalesLink Opportunity
     *
     * @param  mixed $opportunity
     * @param  mixed $hubSpotDeal
     * @return array
     */
    public static function salesLinkOpportunityToHubSpotDealProperties(object $opportunity, object $hubSpotDeal)
    {
        $properties = [];

        // Properties to write to HubSpot:

        //  Price (Deal Amount)
        $amount = new \stdClass();
        $amount->name = 'amount';

        $price = 0;

        if (count($opportunity->productGroups) > 0) {
            $price = array_reduce($opportunity->productGroups, function ($carry, $productGroup) {
                return $carry + array_reduce($productGroup->products, function ($carry2, $product) {
                    return $carry2 + ($product->unitPrice * $product->quantity);
                }, 0);
            }, 0);
        }

        $amount->value = $price;
        array_push($properties, $amount);

        //  Stage
        $stage = new \stdClass();
        $stage->name = 'stage_id';
        $stage->value = $opportunity->stageId;
        array_push($properties, $stage);

        //  Est. Delivery (Close Date)
        $year = $opportunity->estimateDeliveryYear;
        $month = $opportunity->estimateDeliveryMonth;
        if ($year && $month) {
            $closeDate = new \stdClass();
            $closeDate->name = 'closedate';

            $estimatedDelivery = Carbon::parse($year . '-' . $month)->endOfMonth();

            $closeDate->value = $estimatedDelivery->valueOf();
            array_push($properties, $closeDate);
        }

        // Sync time
        $syncTime = new \stdClass();
        $syncTime->name = "last_synced_from_saleslink";
        $syncTime->value = Carbon::now()->toISOString();
        array_push($properties, $syncTime);

        return $properties;
    }

    /**
     * Shortcut to get a property value from a HubSpot object
     *
     * @param  mixed $hubSpotObj
     * @param  string $propertyName
     * @return mixed
     */
    protected static function getHubSpotObjectProperty($hubSpotObj, string $propertyName)
    {
        $property = $hubSpotObj->properties->{$propertyName};

        return isset($property) ? $property->value : null;
    }

    /**
     * Converts from a country name to a country code (e.g. "United States" -> "US")
     *
     * @param  string $country
     * @return string
     */
    protected static function formatCountry(string $country)
    {
        foreach (Countries::DATA as $countryObj) {
            if ($countryObj['name'] === $country) {
                return $countryObj['code'];
            }
        }

        return $country;
    }
}
