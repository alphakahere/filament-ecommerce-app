<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use App\Models\Product;
use App\Models\Variation;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Sum;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationGroup = 'Shop';

    protected static ?string $recordTitleAttribute = 'num_order';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Wizard\Step::make('Order details')
                        ->schema([
                            TextInput::make('num_order')
                                ->default('OR-' . random_int(100000, 999999))
                                ->disabled()
                                ->dehydrated()
                                ->required(),

                            Select::make('customer_id')
                                ->relationship('customer', 'name')
                                ->searchable()
                                ->required(),
                            

                            Select::make('status')
                            ->options([
                                'pending' => 'pending',
                                'processing' => 'processing',
                                'completed' => 'completed',
                                'declined' => 'declined',
                            ])
                            ->default('pending')
                            ->searchable()
                            ->required()
                            ->columnSpanFull(),

                            MarkdownEditor::make('notes')->columnSpanFull()
                        ])->columns(2),

                    Wizard\Step::make('Order Items')
                        ->schema([
                           Repeater::make('items')
                           ->relationship()
                           ->schema([

                                Select::make('product_id')
                                ->label('Product')
                                ->options(Product::query()->pluck('name', 'id')),

                                TextInput::make('quantity')
                                ->numeric()
                                ->default(1)
                                ->required(),

                                TextInput::make('unit_price')
                                ->required(),
                           
                            ])->columns(3)
                        ]),

                ])->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->searchable()->sortable(),
                TextColumn::make('customer.name')->searchable()->sortable(),
                TextColumn::make('status')->searchable()->sortable(),
                TextColumn::make('total')->searchable()->sortable(),
                TextColumn::make('shipping_price')->searchable()->sortable(),
                TextColumn::make('total_price')->searchable()->sortable()->summarize([
                    Sum::make()->money()
                ]),
                TextColumn::make('created_at')->label('Order Date')->date()->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
