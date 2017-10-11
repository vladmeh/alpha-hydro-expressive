require 'compass/import-once/activate'
# Require any additional compass plugins here.

require 'bootstrap-sass'

# Set this to the root of your project when deployed:
http_path = "/"
css_dir = "public/css"
sass_dir = "source/sass"
images_dir = "public/img"
javascripts_dir = "public/js"

# You can select your preferred output style here (can be overridden via the command line):
# output_style = :expanded or :nested or :compact or :compressed
# output_style = :compressed

if environment == :development
    output_style = :nested
end

if environment == :production
#    require 'fileutils'
    output_style = :compressed
#        on_stylesheet_saved do |file|
#            if File.exists?(file)
#            filename = File.basename(file, File.extname(file))
#            File.rename(file, "css" + "/" + filename + ".min" + File.extname(file))
#        end
#    end
end

# To enable relative paths to assets via compass helper functions. Uncomment:
relative_assets = true

# To disable debugging comments that display the original location of your selectors. Uncomment:
line_comments = false


# If you prefer the indented syntax, you might want to regenerate this
# project again passing --syntax sass, or you can uncomment this:
# preferred_syntax = :sass
# and then run:
# sass-convert -R --from scss --to sass source/sass scss && rm -rf sass && mv scss sass

#sourcemap = true
cache = false
