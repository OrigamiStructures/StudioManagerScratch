<?php


namespace App\Model\Table;

use App\Model\Table\PersonCardsTable;

/**
 * Class FatGenericCardsTable
 *
 * All the CardTable variants filter their results to a single member type.
 * So none of them can work on a generic 'Card' index page without loosing
 * significant context (the common root being RolodexCardsTable which lack
 * most of the context layers).
 *
 * So this class lets a well contextualized object that can make meaningful
 * pages.
 *
 * One improvement would be to add the memberships layer in (so Groups can
 * have thier unique context too). And/or extend ArtistCards.
 *
 * Those two changes would make these into Maximum Context Generic cards
 *
 * @package App\Model\Table
 */
class FatGenericCardsTable extends PersonCardsTable
{
    protected function localConditions($query, $options = []) {
        return $query;
    }
}
