class Sender < ApplicationRecord
  has_many :emails

  def image_url
    "https://img.logo.dev/#{top_level_domain}?token=pk_YHpEPFuOTnGDZ6nmBhgIog&retina=true"
  end

  def top_level_domain
    domain = email.split("@").last
    domain_parts = domain.split(".")
    domain_parts.last(2).join(".")
  end
end
