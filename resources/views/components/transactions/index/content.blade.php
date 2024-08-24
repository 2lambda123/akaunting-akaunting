@if ($hideEmptyPage)
    @if (! $hideSummary)
        <x-index.summary>
            <x-slot name="first"
                href="{{ route('transactions.index', ['search' => 'type:income']) }}"
                amount="{{ $summaryItems['incoming_for_humans'] }}"
                title="{{ trans_choice('general.incomes', 1) }}"
                tooltip="{{ $summaryItems['incoming_exact'] }}"
                divider="remove"
            ></x-slot>

            <x-slot name="second"
                href="{{ route('transactions.index', ['search' => 'type:expense']) }}"
                amount="{{ $summaryItems['expense_for_humans'] }}"
                title="{{ trans_choice('general.expenses', 2) }}"
                tooltip="{{ $summaryItems['expense_exact'] }}"
                divider="drag_handle"
            ></x-slot>

            <x-slot name="third"
                amount="{{ $summaryItems['profit_for_humans'] }}"
                title="{{ trans_choice('general.profits', 1) }}"
                tooltip="{{ $summaryItems['profit_exact'] }}"
                class="cursor-default"
            ></x-slot>
        </x-index.summary>
    @endif

    <x-index.container>
        @php
            $search_type = $type == 'income-recurring' ? 'recurring-transactions' : search_string_value('type');
            $active_tab = empty($search_type) ? 'transactions' : (($search_type == 'income') ? 'transactions-income' : (($search_type == 'expense') ? 'transactions-expense' : 'recurring-templates'));
        @endphp

        <x-tabs active="{{ $active_tab }}">
            <x-slot name="navs">
                @if ($search_type == 'income')
                    <x-tabs.nav id="transactions-income" name="{{ trans_choice('general.incomes', 1) }}" active />
                @else
                    <x-tabs.nav-link id="transactions-income" name="{{ trans_choice('general.incomes', 1) }}" href="{{ route('transactions.index', ['search' => 'type:income']) }}" />
                @endif

                @if ($search_type == 'expense')
                    <x-tabs.nav id="transactions-expense" name="{{ trans_choice('general.expenses', 1) }}" active />
                @else
                    <x-tabs.nav-link id="transactions-expense" name="{{ trans_choice('general.expenses', 1) }}" href="{{ route('transactions.index', ['search' => 'type:expense']) }}" />
                @endif

                @if (empty($search_type))
                    <x-tabs.nav id="transactions" name="{{ trans('general.all_type', ['type' => trans_choice('general.transactions', 2)]) }}" active />
                @else
                    <x-tabs.nav-link id="transactions" name="{{ trans('general.all_type', ['type' => trans_choice('general.transactions', 2)]) }}" href="{{ route('transactions.index') }}" />
                @endif

                <x-tabs.nav-link id="recurring-templates" name="{{ trans_choice('general.recurring_templates', 2) }}" href="{{ route('recurring-transactions.index') }}" />
            </x-slot>

            <x-slot name="content">
                @if ((! $hideSearchString) && (! $hideBulkAction))
                    <x-index.search
                        search-string="App\Models\Banking\Transaction"
                        bulk-action="App\BulkActions\Banking\Transactions"
                    />
                @elseif ((! $hideSearchString) && $hideBulkAction)
                    <x-index.search
                        search-string="App\Models\Banking\Transaction"
                    />
                @elseif ($hideSearchString && (! $hideBulkAction))
                    <x-index.search
                        bulk-action="App\BulkActions\Banking\Transactions"
                    /> 
                @endif

                @if ($active_tab != 'recurring-templates')
                    <x-tabs.tab id="{{ $active_tab }}">
                        <x-transactions.index.transaction :type="$type" :transactions="$transactions" />
                    </x-tabs.tab>
                @else
                    <x-tabs.tab id="recurring-templates">
                        <x-transactions.index.recurring_templates :type="$type" :transactions="$transactions" />
                    </x-tabs.tab>
                @endif
            </x-slot>
        </x-tabs>
    </x-index.container>

    <akaunting-connect-transactions
        :show="connect.show"
        :transaction="connect.transaction"
        :currency="connect.currency"
        :documents="connect.documents"
        :translations="connect.translations"
        modal-dialog-class="max-w-screen-lg"
        v-on:close-modal="connect.show = false"
    ></akaunting-connect-transactions>
@else
    @if ($type == 'income-recurring')
        <x-empty-page
            group="banking"
            page="recurring_templates"
            permission-create="create-banking-transactions"
            :buttons="[
                [
                    'url'           =>  route('recurring-transactions.create', ['type' => 'income-recurring']),
                    'permission'    => 'create-banking-transactions',
                    'text'          => trans('general.title.new', ['type' =>  trans_choice('general.recurring_incomes', 1)]),
                    'description'   => '',
                    'active_badge'  => true,
                ],
                [
                    'url'           => route('recurring-transactions.create', ['type' => 'expense-recurring']),
                    'permission'    => 'create-banking-transactions',
                    'text'          => trans('general.title.new', ['type' =>  trans_choice('general.recurring_expenses', 1)]),
                    'description'   => '',
                    'active_badge'  => true,
                ],
                [
                    'url'           => route('import.create', ['banking', 'recurring-transactions']),
                    'permission'    => 'create-banking-transactions',
                    'text'          => trans('import.title', ['type' => trans_choice('general.recurring_transactions', 2)]),
                    'description'   => trans('general.empty.actions.import', ['type' => strtolower(trans_choice('general.recurring_transactions', 2))]),
                    'active_badge'  => false,
        ],
            ]"
        />
    @else
        <x-empty-page
            group="banking"
            page="transactions"
            hide-button-import
            :buttons="[
                [
                    'url' =>  route('transactions.create', ['type' => 'income']),
                    'permission' => 'create-banking-transactions',
                    'text' => trans('general.title.new', ['type' => trans_choice('general.incomes', 1)]),
                    'description' => trans('general.empty.actions.new', ['type' => trans_choice('general.incomes', 1)]),
                    'active_badge' => false
                ],
                [
                    'url' => route('transactions.create', ['type' => 'expense']),
                    'permission' => 'create-banking-transactions',
                    'text' => trans('general.title.new', ['type' => trans_choice('general.expenses', 1)]),
                    'description' => trans('general.empty.actions.new', ['type' => trans_choice('general.expenses', 1)]),
                    'active_badge' => false
                ],
            ]"
        />
    @endif
@endif