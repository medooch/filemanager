# Medooch Filemanager

Symfony3 FileManager Bundle.

Note : Compatible with symfony2.

This bundle provider a simple filemanager based on symfony3 framework.

# Install

Add this to your composer.json

    "medooch/filemanager" : "dev-develop"
    
Run "composer update"

Enable the bundle

            new Core\FilemanagerBundle\FilemanagerBundle(),
            new Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
            new Liip\ImagineBundle\LiipImagineBundle(),
            
add this to config.yml
    
    stof_doctrine_extensions:
        default_locale: '%locale%'
        translation_fallback: true
        orm:
            default:
                tree: true
                sluggable: false
                translatable: false
                timestampable: true
                softdeleteable: false
                blameable: true
                
    liip_imagine:
        resolvers:
            default:
              web_path: ~
        filter_sets:
            cache: ~
            # Image preview
            image_preview:
                quality: 75
                filters:
                    auto_rotate: ~
                    strip: ~
                    thumbnail: { size: [100, 60], mode: inset }
                    
import bundle routing:
    
    filemanager:
        resource: "@FilemanagerBundle/Resources/config/routing.yml"
        prefix:   /filemanager
        
Note : The filemanager by default import the LiipImagine routing, you don't need to redefine it.