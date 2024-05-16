<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Illuminate\Database\Eloquent\Model;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-bolt';


    protected static ?int $navigationSort = 1;

    protected static ?string $navigationGroup = 'Shop';

    protected static ?int $globalSearchResultLimit = 20;


    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'slug', 'sku'];
    }
    
    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'name' => $record->name,
            'Brand' => $record->brand->name,
        ];
    }


    public static function form(Form $form): Form
    {
        return $form
        ->schema([

           Group::make()
                ->schema([
                   Section::make()
                        ->schema([
                           TextInput::make('name')
                                ->required()
                                ->maxLength(255)
                                ->live(onBlur: true)
                                ->afterStateUpdated(function (string $operation, $state, Forms\Set $set) {
                                    if ($operation !== 'create') {
                                        return;
                                    }
                                    $abbreviation = '';
                                    foreach (explode(' ', $state) as $word) {
                                        $abbreviation .= Str::upper(substr($word, 0, 1));
                                    }

                                    $set('slug', Str::slug($state));
                                    $set('sku', 'SKU' . $abbreviation. random_int(1000000, 9999999));
                                }),

                           TextInput::make('slug')
                                ->disabled()
                                ->dehydrated()
                                ->required()
                                ->maxLength(255)
                                ->unique(Product::class, 'slug', ignoreRecord: true),

                           MarkdownEditor::make('description')
                                ->columnSpan('full'),
                                
                        ])
                        ->columns(2),

                    Section::make('Images')
                        ->schema([
                            FileUpload::make('image')
                                ->hiddenLabel(),
                        ])
                        ->collapsible(),

                    
                    Section::make('Pricing & Inventory')
                        ->schema([
                            TextInput::make('sku')
                                ->label('SKU (Stock Keeping Unit)')
                                ->unique(Product::class, 'sku', ignoreRecord: true)
                                ->maxLength(255)
                                ->required(),

                            TextInput::make('quantity')
                                ->numeric()
                                ->maxLength(255)
                                ->required(),
                            
                           TextInput::make('price')
                                ->numeric()
                                ->rules(['regex:/^\d{1,6}(\.\d{0,2})?$/'])
                                ->required(),

                                TextInput::make('old_price')
                                ->label('Compare at price')
                                ->numeric()
                                ->rules(['regex:/^\d{1,6}(\.\d{0,2})?$/'])
                                ->required(),

                           TextInput::make('cost')
                                ->label('Cost per item')
                                ->helperText('Customers won\'t see this price.')
                                ->numeric()
                                ->rules(['regex:/^\d{1,6}(\.\d{0,2})?$/'])
                                ->required(),
                        ])
                        ->columns(2),


                    Section::make('Product options')
                        ->schema([
                            KeyValue::make('options')
                                ->keyLabel('Option name')
                                ->valueLabel('Option value')
                                ->reorderable(),
                        ]),
                    
                   Section::make('Shipping')
                        ->schema([
                           Checkbox::make('is_can_be_returned')
                                ->label('This product can be returned')->default(false),

                           Checkbox::make('is_can_be_shipped')
                                ->label('This product will be shipped')->default(true),
                        ])
                        ->columns(2),
                ])
                ->columnSpan(['lg' => 2]),

           Group::make()
                ->schema([
                   Section::make('Status')
                        ->schema([
                           Toggle::make('is_visible')
                                ->label('Visible')
                                ->helperText('This product will be hidden from all sales channels.')
                                ->default(true),

                            Toggle::make('is_featured')
                                ->label('Featured')
                                ->helperText('This product will be highlighted ')
                                ->default(false),
                            
                            Toggle::make('is_promoted')
                                ->label('Promoted')
                                ->helperText('This product will be promoted ')
                                ->default(false),

                           DatePicker::make('published_at')
                                ->label('Published at')
                                ->default(now())
                                ->required(),
                        ]),

                   Section::make('Associations')
                        ->schema([
                           Select::make('brand_id')
                                ->relationship('brand', 'name')
                                // ->searchable()
                                ->required(),

                           Select::make('categories')
                                ->relationship('categories', 'name')
                                ->multiple()
                                ->required(),

                            Select::make('provider_id')
                                ->relationship('provider', 'name')
                                ->required()
                                ->placeholder('select a provider'),
                            
                        ]),
                    ])
                ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
        
            

    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('sku')->sortable()->toggleable(),
                TextColumn::make('name')->sortable()->searchable(),
                TextColumn::make('brand.name')->sortable()->searchable(),
                TextColumn::make('quantity')->sortable(),
                TextColumn::make('categories.name')->sortable()->searchable()->toggleable(),
                ToggleColumn::make('is_visible')->label('Visibility')->sortable(),
                TextColumn::make('price')->sortable(),
                TextColumn::make('old_price')->sortable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('cost')->sortable()->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_featured')->boolean()->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_promoted')->boolean()->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_can_be_returned')->boolean()->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_can_be_shipped')->boolean()->toggleable(isToggledHiddenByDefault: true),

            ])
            ->filters([
                TernaryFilter::make('is_visible'),
                SelectFilter::make('brand')->relationship('brand', 'name'),
                SelectFilter::make('categories')->relationship('categories', 'name'),
                TernaryFilter::make('is_promoted'),
                TernaryFilter::make('is_featured'),
                TernaryFilter::make('is_can_be_returned'),
                TernaryFilter::make('is_can_be_shipped'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
