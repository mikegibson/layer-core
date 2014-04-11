<?php

namespace Layer\Data;

use Illuminate\Database\Query\Builder;

class QueryBuilder extends Builder {

    /**
     * @return $this
     * @todo Is this needed?
     */
    public function resetLimit() {
        $this->limit = $this->offset = null;
        return $this;
    }

}